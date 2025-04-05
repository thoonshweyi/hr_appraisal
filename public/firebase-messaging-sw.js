importScripts('https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.6.10/firebase-messaging-compat.js');

firebase.initializeApp({
    apiKey: "AIzaSyBpHbI8rMw6pWjWalY5yF2jjOc2dOYwQ3Q",
    authDomain: "hr-appraisal.firebaseapp.com",
    projectId: "hr-appraisal",
    storageBucket: "hr-appraisal.firebasestorage.app",
    messagingSenderId: "254575584163",
    appId: "1:254575584163:web:66f30fcd9199f938eb7016",
    measurementId: "G-X1625951R8"
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage(function(payload) {
    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
        data: {
            click_action: payload.data.click_action || payload.data.link
        }
    };

    self.registration.showNotification(notificationTitle, notificationOptions);
});

self.addEventListener('notificationclick', function(event) {
    const click_action = event.notification.data?.click_action || 'https://your-fallback-url.com';
    event.notification.close();
    event.waitUntil(clients.openWindow(click_action));
});
