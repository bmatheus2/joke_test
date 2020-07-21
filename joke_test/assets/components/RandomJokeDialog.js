import React, { useEffect, useState } from 'react';
import {
    Button,
    Dialog,
    DialogActions,
    DialogContent,
    DialogContentText,
    DialogTitle,
    TextField
} from '@material-ui/core';
import axios from 'axios';

export default function RandomJokeDialog({ onClose, showRandomJokeDialog }) {

    const [joke, setJoke] = useState(null);

    useEffect(() => {
        if(!showRandomJokeDialog) {
            getRandomJoke();
        }
    }, [showRandomJokeDialog]);

    const dialogContentText = () => {
        if(joke) {
            return joke.content;
        }
    }

    const dialogTitle = () => {
        if(joke) {
            return `Joke ID: ${joke.id}`;
        }
    }

    const getRandomJoke = async () => {
        const res = await axios.get('/joke/random');
        setJoke(res.data.data);
    }

    return (
        <Dialog open={showRandomJokeDialog} onClose={() => onClose()} aria-labelledby="form-dialog-title">
            <DialogTitle id="form-dialog-title">{dialogTitle()}</DialogTitle>
            <DialogContent>
                {dialogContentText()}
                <DialogActions>
                    <Button onClick={() => onClose()} color="primary">
                        Close
                    </Button>
                </DialogActions>
            </DialogContent>
        </Dialog>
    );
}
