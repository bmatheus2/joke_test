import React, { useEffect, useState } from 'react';
import {
    Button,
    Dialog,
    DialogActions,
    DialogContent,
    DialogTitle,
    TextField
} from '@material-ui/core';
import axios from 'axios';

export default function EditJokeDialog({ joke, onClose, onError, onSave, showEditDialog }) {

    const [jokeId, setJokeId] = useState(null);
    const [jokeText, setJokeText] = useState('');

    useEffect(() => {
        if(joke) {
            setJokeId(joke.id);
            setJokeText(joke.content);
        }
    }, [joke]);

    useEffect(() => {
        if(!showEditDialog) {
            // Clear after modal fadeout animation complete
            setTimeout(() => {
                setJokeId(null);
                setJokeText('');
            }, 200);
        }
    }, [showEditDialog]);

    const dialogTitle = () => {
        return (jokeId) ? `Editing Joke ${jokeId}` : 'Add New Joke';
    }

    const jokeTextChanged = (e) => {
        setJokeText(e.target.value);
    }

    const save = async () => {
        try {
            const { data } = await axios.post(endpoint(), {
                content: jokeText
            });
            onSave(data.data.id);
        } catch(e) {
            console.log(e);
            if (e.response) {
             onError(e.response);
           }
        }
    }

    const endpoint = () => {
        return (jokeId) ? `/joke/${jokeId}` : '/joke';
    }

    return (
        <Dialog open={showEditDialog} onClose={() => onClose()} aria-labelledby="form-dialog-title">
            <DialogTitle id="form-dialog-title">{dialogTitle()}</DialogTitle>
            <DialogContent>
                <TextField
                    autoFocus
                    defaultValue={jokeText}
                    onChange={jokeTextChanged}
                    margin="dense"
                    multiline={true}
                    id="name"
                    label="Joke"
                    rows="4"
                    type="email"
                    fullWidth
                    style={{minWidth: '320px'}}
                />
            </DialogContent>
            <DialogActions>
                <Button onClick={() => onClose()} color="primary">
                    Cancel
                </Button>
                <Button onClick={() => save(joke, onClose)} color="primary">
                    Save
                </Button>
            </DialogActions>
        </Dialog>
    );
}
