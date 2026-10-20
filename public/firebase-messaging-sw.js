importScripts('https://www.gstatic.com/firebasejs/10.12.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.12.0/firebase-messaging-compat.js');

firebase.initializeApp({
    apiKey: "AIzaSyD_k_OfmwBLa0BLQEkCHDkBNsiirFizKb4",
    authDomain: "petnanny-51b57.firebaseapp.com",
    projectId: "petnanny-51b57",
    storageBucket: "petnanny-51b57.firebasestorage.app",
    messagingSenderId: "864638678858",
    appId: "1:864638678858:web:16b61f47459aa2db4b73b3"
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
    console.log('[firebase-messaging-sw.js] Received background message ', payload);
    const notificationTitle = payload.notification?.title || 'PetNanny Notification';
    const notificationOptions = {
        body: payload.notification?.body || 'You have a new notification.',
        icon: '/assets/logo/PetNanny_Logo.png'
    };

    self.registration.showNotification(notificationTitle, notificationOptions);
});
