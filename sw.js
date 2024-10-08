console.log('service working is running!');

const ch100 = new BroadcastChannel('ch100');

let state = {
  allow_notification: 'off'
}

ch100.onmessage = evt => {
  if (evt.data && evt.data.to == 'service-worker') {
    if (evt.data.evt == 'state-updated') {
      console.log('@service-worker : state-updated');
      console.log(evt.data.state);

      state = evt.data.state;
    }
  }
}

self.addEventListener('push', function(event) {
    if (state.allow_notification == 'on') {
      self.registration.showNotification('ServiceWorker Cookbook', {
        body: 'Alea iacta est x ',
      })

      /*
      event.waitUntil(
          self.registration.showNotification('ServiceWorker Cookbook', {
            body: 'Alea iacta est x ',
          })
      )
      */
    }
})

/*
self.addEventListener('pushsubscriptionchange', (event) => {
  console.log('pushsubscriptionchange-event');
  console.log(event);
})
*/