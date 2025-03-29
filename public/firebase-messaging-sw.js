// public/firebase-messaging-sw.js

importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-messaging.js');
// Your Firebase config from the web
const firebaseConfig = {
  apiKey: "AIzaSyBpHbI8rMw6pWjWalY5yF2jjOc2dOYwQ3Q",
  authDomain: "hr-appraisal.firebaseapp.com",
  projectId: "hr-appraisal",
  storageBucket: "hr-appraisal.firebasestorage.app",
  messagingSenderId: "254575584163",
  appId: "1:254575584163:web:66f30fcd9199f938eb7016",
  measurementId: "G-X1625951R8"
};

firebase.initializeApp(firebaseConfig);

const messaging = firebase.messaging();
