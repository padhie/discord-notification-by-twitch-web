class NotificationHandler {
    constructor(currentUser, testUrl) {
        this.currentUser = currentUser;
        this.testUrl = testUrl;

        this.userArea = 'box-' + currentUser;

        this.init();
    }

    init() {
        document.getElementById('submit').addEventListener('click', () => {
            document.getElementById('form').submit();
        });
        document.getElementById('addBox').addEventListener('click', () => {
            this.addBox();
        });

        let testBoxElements = document.getElementsByClassName('testHook');
        for (let i=0; i<testBoxElements.length; i++) {
            testBoxElements[i].addEventListener('click', (event) => {
                console.log(event.target);
                this.testHook(event.target);
            });
        }

        let closeBoxElements = document.getElementsByClassName('removeBox');
        for (let j=0; j<closeBoxElements.length; j++) {
            closeBoxElements[j].addEventListener('click', (event) => {
                this.removeBox(event.target);
            });
        }
    }

    addBox() {
        this.addUserArea();

        let div = document.createElement('div');
        div.classList.add('col-md-4');
        div.classList.add('float-left');
        div.classList.add('mt-2');
        div.classList.add('mb-3');

        let card = document.createElement('div');
        card.classList.add('card');
        card.classList.add('bg-light');
        div.append(card);

        let cardBody = document.createElement('div');
        cardBody.classList.add('card-body');
        card.append(cardBody);

        card.append(this.createNewBadge());
        card.append(this.createCloseIcon());

        cardBody.append(this.createChannelDiv());
        cardBody.append(this.createWebhookDiv());
        cardBody.append(this.createMessageDiv());

        document.getElementById(this.userArea).append(div);
    }

    addUserArea() {
        if (document.getElementById(this.userArea) !== null) {
            return;
        }

        let div = document.createElement('div');
        div.id = this.userArea;
        div.classList.add('col-md-12');
        div.classList.add('row');

        let headline = document.createElement('h1');
        headline.classList.add('h1');
        headline.classList.add('col-md-12');
        headline.classList.add('border-bottom');
        headline.innerText = this.currentUser;

        document.getElementById('cardList').append(div);

        div.append(headline);
    }

    removeBox(element) {
        let box = element.parentElement.parentElement;
        let parendBox = box.parentNode;

        parendBox.removeChild(box);
    }

    createChannelDiv() {
        let formGroup = document.createElement('div');
        formGroup.classList.add('form-group');

        let label = document.createElement('label');
        label.attributes.for = 'streamer_channel';
        label.innerText = 'Channel';
        formGroup.append(label);

        let input = document.createElement('input');
        input.classList.add('form-control');
        input.type = 'text';
        input.id = 'streamer_channel';
        input.name = 'newStreamer[channel][]';
        formGroup.append(input);

        return formGroup;
    }

    createWebhookDiv() {
        let formGroup = document.createElement('div');
        formGroup.classList.add('form-group');

        let label = document.createElement('label');
        label.attributes.for = 'streamer_webhook';
        label.innerText = 'WebHook URL';
        formGroup.append(label);

        let input = document.createElement('input');
        input.classList.add('form-control');
        input.type = 'text';
        input.id = 'streamer_webhook';
        input.name = 'newStreamer[webHook][]';
        formGroup.append(input);

        return formGroup;
    }

    createMessageDiv() {
        let formGroup = document.createElement('div');
        formGroup.classList.add('form-group');

        let label = document.createElement('label');
        label.attributes.for = 'streamer_webhook';
        label.innerText = 'Message';
        formGroup.append(label);

        let input = document.createElement('textarea');
        input.classList.add('form-control');
        input.id = 'streamer_message';
        input.rows = 5
        input.name = 'newStreamer[message][]';
        formGroup.append(input);

        return formGroup;
    }

    createNewBadge() {
        let badge = document.createElement('spam');

        badge.classList.add('badge');
        badge.classList.add('badge-warning');
        badge.classList.add('testHook');
        badge.classList.add('ml-1');
        badge.classList.add('mt-1');

        badge.innerText = 'New';

        return badge;
    }

    createCloseIcon() {
        let icon = document.createElement('spam');

        icon.classList.add('fa');
        icon.classList.add('fa-times');
        icon.classList.add('removeBox');
        icon.classList.add('mr-1');
        icon.classList.add('mt-1');
        icon.classList.add('text-danger');

        icon.title = "remove";

        return icon;
    }

    testHook(element) {
        let id = element.dataset.id;
        if (id === '') {
            return;
        }

    fetch(this.testUrl + '?id=' + id)
    .then(response => response.json())
            .then(data => console.log(data));
    }
}