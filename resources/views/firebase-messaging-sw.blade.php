importScripts('https://www.gstatic.com/firebasejs/10.12.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.12.0/firebase-messaging-compat.js');

firebase.initializeApp({
    apiKey: "{{ config('firebase.web.api_key') }}",
    authDomain: "{{ config('firebase.web.auth_domain') }}",
    projectId: "{{ config('firebase.web.project_id') }}",
    storageBucket: "{{ config('firebase.web.storage_bucket') }}",
    messagingSenderId: "{{ config('firebase.web.messaging_sender_id') }}",
    appId: "{{ config('firebase.web.app_id') }}"
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
