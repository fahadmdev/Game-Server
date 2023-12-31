'use strict';

const node = document.getElementById('app');

// Update the second argument to `Elm.Main.embed` with your selected API. See
// the Intro section of the technical assessment documentation for more
// information:
// https://technical-assessment.konicaminoltamarketplace.com
const app = Elm.Main.embed(node, {
    api: 'Http',
    hostname: 'http://127.0.0.1:8000',
});

app.ports.startTimer.subscribe((int) => {
    setTimeout(() => {
        app.ports.timeout.send(int);
    }, 10000);
});
