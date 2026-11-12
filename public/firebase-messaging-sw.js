importScripts('https://www.gstatic.com/firebasejs/10.12.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.12.0/firebase-messaging-compat.js');

firebase.initializeApp({
    apiKey: "AIzaSyD_k_OfmwBLaQBLQEKhDKbNSiirFizKb4",
    authDomain: "petnanny-51b57.firebaseapp.com",
    projectId: "petnanny-51b57",
    storageBucket: "petnanny-51b57.firebasestorage.app",
    messagingSenderId: "864638678858",
    appId: "1:864638678858:web:16b61f47459aa2db4b73b3"
});

const messaging = firebase.messaging();

// 1. Handle background messages
messaging.onBackgroundMessage((payload) => {
    console.log('[firebase-messaging-sw.js] Received background message ', payload);

    const notificationTitle = payload.notification?.title || 'PetNanny Notification';
    const notificationOptions = {
        body: payload.notification?.body || 'You have a new notification.',
        icon: '/assets/logo/PetNanny_Logo.png',
        data: payload.data // Include data for redirect on click
    };

    self.registration.showNotification(notificationTitle, notificationOptions);
});

// 2. Handle notification click
self.addEventListener('notificationclick', function(event) {
    event.notification.close(); // Close the notification on click

    // Get the URL from payload, default to homepage '/'
    const urlToOpen = event.notification.data?.url || '/';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(windowClients => {
            // Focus existing tab if URL matches
            for (var i = 0; i < windowClients.length; i++) {
                var client = windowClients[i];
                if (client.url === urlToOpen && 'focus' in client) {
                    return client.focus();
                }
            }
            // Otherwise, open a new tab
            if (clients.openWindow) {
                return clients.openWindow(urlToOpen);
            }
        })
    );
});
