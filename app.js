let sw_registration = null;
const ch100 = new BroadcastChannel('ch100');

let state = {
    allow_notification: $.default(localStorage.getItem('allow_notification'), 'off')
}

function set_state(key, value) {
    state[key] = value;

    if (key == 'allow_notification') {
        localStorage.setItem('allow_notification', value);    
    }

    on_state_updated();
}

function on_state_updated() {
    console.log('on_update_stated');
    console.log(state);

    let app = $('#app');
    
    if (state.allow_notification == 'on') {
        app.removeClass('allow-notification-off');
        app.addClass('allow-notification-on');
    } else {
        app.removeClass('allow-notification-on');
        app.addClass('allow-notification-off');
    }

    ch100.postMessage({
        to: 'service-worker',
        evt: 'state-updated',
        state: state
    })
}

function turn_on_notification() {
    Notification.requestPermission().then(permission => {
        if (permission == 'granted') {
            set_state('allow_notification', 'on');
            subscribe();
        }            
    })
}

function turn_off_notification() {
    set_state('allow_notification', 'off');
}

function urlBase64ToUint8Array(base64String) {
    var padding = '='.repeat((4 - base64String.length % 4) % 4);
    var base64 = (base64String + padding)
        .replace(/\-/g, '+')
        .replace(/_/g, '/');

    var rawData = window.atob(base64);
    var outputArray = new Uint8Array(rawData.length);

    for (var i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }

    return outputArray;
}

function subscribe() {
    if (!("Notification" in window)) return;
    
    sw_registration.pushManager.getSubscription().then(async function(subscription) {
        if (subscription) {
            return subscription;
        }

        const vapidPublicKey = 'BKFlHXxr1X70rH0Gjn1tkNPwZxg50AVUHgehZmAi36fkXNpe9oJN2ww6s-kcX6ttDI4DtzmeuTL2BkUBIRynX4I';
        const convertedVapidKey = urlBase64ToUint8Array(vapidPublicKey);

        return sw_registration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: convertedVapidKey
        })
    }).then(function(subscription) {
        console.log(JSON.stringify(subscription));

        $.post_json('./subscribe.php', subscription).then(res => {
            console.log(res);
        })

        return subscription;
    })                
}

function unsubscribe() {
    set_state('allow_notification', 'off');
}

function push_message() {
    $.get_json('./push.php');
}

navigator.serviceWorker.ready.then(registration => {
    console.log('service worker is ready!');
    sw_registration = registration;

    on_state_updated();

    if (state.allow_notification == 'on') {
        subscribe();
    }
})

navigator.serviceWorker.register('sw.js');

$.ready(() => {
    let app = $('#app');

    app.on('click', evt => {
        let el = evt.target;

        if (el.matches('[data-role="toggle-switch"]')) {
            let allow_notification = state.allow_notification=='on'?'off':'on'

            if (allow_notification == 'on') {
                Notification.requestPermission().then(permission => {
                    if (permission == 'granted') {
                        set_state('allow_notification', 'on');    
                    }
                })
            } else {
                set_state('allow_notification', 'off');    
            }
        } else if (el.matches('button[data-cmd="push-message"]')) {
            push_message();
        }
    })
})