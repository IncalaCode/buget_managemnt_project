class Notyfclass {
    constructor() {

        this.loadingNotification = null;
        this.notyf = new Notyf({
            position: {
                x: 'right',
                y: 'top',
            },
            types: [
                {
                    type: 'warning',
                    background: 'orange',
                    duration: 3000,
                    dismissible: true
                },
                {
                    type: 'error',
                    background: 'indianred',
                    duration: 3000,
                    dismissible: true
                },
                {
                    type: 'loading',
                    background: 'blue',
                    duration: 300000,
                    dismissible: false
                },
                {
                    type: 'success',
                    background: 'green',
                    duration: 3000,
                    dismissible: true
                },
                {
                    type: 'info',
                    background: '#21ff9f',
                    duration: 3000,
                    dismissible: true
                }
            ]
        });
    }

    showMessage(type, message, isLoading = false, is_dissims = false) {

        if (!this.notyf) {
            console.error('Notyf instance is not initialized.');
            return;
        }

        // Dismiss the current notification if needed
        if (is_dissims && this.loadingNotification) {
            this.notyf.dismiss(this.loadingNotification);
        }

        // Show the new notification
        this.loadingNotification = this.notyf.open({
            type: type,
            message: message + (isLoading ? `<div class="d-flex justify-content-end">
                <div class="spinner-border text-primary" style="height: 1rem; width: 1rem;" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>` : ''),
        });
    }

    dismiss(type, message) {
        if (!this.notyf) {
            console.error('Notyf instance is not initialized.');
            return;
        }

        if (this.loadingNotification) {
            this.notyf.dismiss(this.loadingNotification);
            setTimeout(() => {
                this.notyf.open({
                    type: type,
                    message: message
                });
            }, 2000);
        }
    }
}

var NotyfService = new Notyfclass();

export default NotyfService;
