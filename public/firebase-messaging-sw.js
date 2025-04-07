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

// messaging.onBackgroundMessage(function(payload) {
//   console.log('[firebase-messaging-sw.js] Received background message ', payload);

//   const notificationTitle = payload.notification.title;
//   const link = payload.data.link || 'https://yourdomain.com';

//   const notificationOptions = {
//     body: payload.notification.body,
//     data: {
//       link: link,
//     },
//   };

//   self.registration.showNotification(notificationTitle, notificationOptions);
// });

// self.addEventListener('notificationclick', function(event) {
//   event.notification.close();
//   const link = event.notification.data.link;

//   event.waitUntil(
//     clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function(clientList) {
//       for (const client of clientList) {
//         if (client.url === link && 'focus' in client) {
//           return client.focus();
//         }
//       }
//       if (clients.openWindow) {
//         return clients.openWindow(link);
//       }
//     })
//   );
// });
