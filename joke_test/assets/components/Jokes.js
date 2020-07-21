import React, { nextTick, useEffect, useState } from 'react';
import {
    AppBar,
    Box,
    Button,
    Container,
    FormControl,
    FormGroup,
    FormLabel,
    IconButton,
    InputBase,
    List,
    ListItem,
    ListItemText,
    ListItemSecondaryAction,
    MenuItem,
    Select,
    Snackbar,
    TextField,
    Toolbar,
    Typography
} from '@material-ui/core';
import { Alert, Pagination } from '@material-ui/lab';
import {
    Add as AddIcon,
    Delete as DeleteIcon,
    Edit as EditIcon,
    Launch as LaunchIcon,
    Menu as MenuIcon,
    Search as SearchIcon
} from '@material-ui/icons';
import { makeStyles } from '@material-ui/core/styles';
import RandomJokeDialog from './RandomJokeDialog';

const useStyles = makeStyles((theme) => ({
  root: {
    flexGrow: 1,
  },
  menuButton: {
    marginRight: theme.spacing(2),
  },
  title: {
    flexGrow: 1,
  },
}));
import EditJokeDialog from './EditJokeDialog';
import axios from 'axios';

export default function Jokes() {

  const classes = useStyles();

  const [joke, setJoke] = useState(null);
  const [jokes, setJokes] = useState([]);
  const [page, setPage] = useState(1);
  const [perPage, setPerPage] = useState(10);
  const [search, setSearch] = useState('');
  const [searchBy, setSearchBy] = useState('text');
  const [showEditDialog, setEditDialog] = useState(false);
  const [showRandomJokeDialog, setShowRandomJokeDialog] = useState(false);
  const [showSnackbar, setShowSnackbar] = useState(false);
  const [snackbarText, setSnackbarText] = useState('');
  const [snackbarType, setSnackbarType] = useState('info');
  const [totalPages, setTotalPages] = useState(0);
  const [updating, setUpdating] = useState(true);

  useEffect(() => {
      getJokes();
  }, []);

  useEffect(() => {
      if(searchBy == 'text') {
          getJokes();
      }
  }, [page]);

  useEffect(() => {
      if(!showSnackbar) {
          setSnackbarType('info');
      }
  }, [showSnackbar]);

  const getJokes = async (ignoreParams = false) => {
      const params = (ignoreParams) ? {} : queryParams();
      const { data } = await axios.get('/joke/list', params);
      setJokes(data.jokes);
      setTotalPages(data.total_pages);
      setUpdating(false);
  }

  const queryParams = () => {
      return {
          params: {
              page: page,
              'per-page': perPage,
              search: search,
              type: searchBy
          }
      }
  }

  const getSingleJoke = async (id) => {
      return await axios.get(`/joke/${id}`);
  }

  const clear = async () => {
      setSearch('');
      setSearchBy('text');
      setPerPage(10);
      setPage(1);
      getJokes(true);
  }

  const deleteJoke = async (joke) => {
      setUpdating(true);
      const res = await axios.delete(`/joke/${joke.id}`);
      showAlert(`Deleted Joke ${joke.id}`, 'warning');
      getJokes();
  }

  const showAlert = (text = '', type = 'info') => {
      setSnackbarText(text);
      setSnackbarType(type);
      setShowSnackbar(true);
  }

  const addJoke = () => {
      setEditDialog(true);
  }

  const editJoke = (joke) => {
      setJoke(joke);
      setEditDialog(true);
  }

  const handleDialogClose = (e) => {
      setEditDialog(false);
      setTimeout(() => {
          setJoke(null);
      }, 300);
  }

  const handleDialogSave = (newJokeId) => {
      setUpdating(true);
      showAlert(`Saved Joke ${newJokeId}`, 'success');
      handleDialogClose();
      getJokes();
  }

  const handlePageChange = (e, page) => {
      setPage(page);
  }

  const onError = (e) => {
      showAlert(`Error status ${e.status}, check console for more details.`, 'error');
      console.error(e.data.errors);
  }

  const searchAction = () => {
      if(searchBy == 'text') {
          (page > 1) ? setPage(1) : getJokes();
      } else {
          searchById(search);
      }
  }

  const searchById = async (id) => {
      try {
          const { data } = await getSingleJoke(id);
          setJokes([data]);
          setTotalPages(1);
          setPage(1);
      } catch(e) {
          console.log(e.response);
          onError(e.response);
      }
  }

  const showRandomJoke = () => {
      setShowRandomJokeDialog(true);
  }

  const textFieldType = () => {
      return (searchBy == 'id') ? 'number' : searchBy;
  }

  const handleRandomJokeClose = () => {
      setShowRandomJokeDialog(false);
  }

  const handleSnackbarClose = (event, reason) => {
       if (reason === 'clickaway') {
         return;
       }
       setShowSnackbar(false);
     }

  return (
      <Container>
        <div className={classes.root}>
            <AppBar position="static">
              <Toolbar>
                <Typography variant="h6" noWrap className={classes.title}>
                  Jokes Test
                </Typography>
                <Button variant="contained" className={classes.menuButton} onClick={() => showRandomJoke()}>
                    <LaunchIcon /> Random Joke
                </Button>
                <Button variant="contained" onClick={() => addJoke()}>
                    <AddIcon /> Add Joke
                </Button>
              </Toolbar>
            </AppBar>
        </div>
        <Box style={{padding: '10px 10px 0 10px'}}>
            <Select
                value={searchBy}
                onChange={(e) => setSearchBy(e.target.value)}
            >
                <MenuItem value="text">Search by joke text</MenuItem>
                <MenuItem value="id">Search by joke id</MenuItem>
            </Select>
            <TextField placeholder={`Search ${searchBy}`} value={search} type={(searchBy == 'id') ? 'number' : 'text'} onChange={(e) => setSearch(e.target.value)} />
            <Button color="primary" size="small" variant="contained" onClick={() => searchAction()}>Go</Button>
            <Button size="small" onClick={() => clear()}>Clear</Button>
        </Box>
        <hr />
        <List>
        {jokes.map((joke, index) => {
            return <ListItem key={index} style ={ index % 2? { background : "#f7f7f7" }:{ background : "#ffffff" }}>
              <ListItemText
                primary={joke.content} style={{paddingRight: '50px'}}
              />
              <ListItemSecondaryAction>
                 <IconButton aria-label="edit" disabled={updating} onClick={() => editJoke(joke)}>
                  <EditIcon />
                </IconButton>
                <IconButton edge="end" aria-label="delete" disabled={updating} onClick={() => deleteJoke(joke)}>
                  <DeleteIcon color="secondary" />
                </IconButton>
              </ListItemSecondaryAction>
            </ListItem>
        })}
        </List>
        <EditJokeDialog joke={joke} onError={onError} showEditDialog={showEditDialog} onClose={handleDialogClose} onSave={handleDialogSave} />
        <RandomJokeDialog showRandomJokeDialog={showRandomJokeDialog} onClose={handleRandomJokeClose} />
        <hr />
        <Pagination count={totalPages} page={page} onChange={handlePageChange} />
        <Snackbar open={showSnackbar} autoHideDuration={3000} onClose={handleSnackbarClose}>
          <Alert onClose={handleSnackbarClose} severity={snackbarType}>
            {snackbarText}
          </Alert>
        </Snackbar>
      </Container>
  );
}
