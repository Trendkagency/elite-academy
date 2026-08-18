// Firebase Messaging Service Worker for Elite Academy LMS
importScripts('https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.23.0/firebase-messaging-compat.js');

firebase.initializeApp({
  apiKey: 'AIzaSyCmAS3q2VNvCbKhrfKtC8hn163GX7116Ns',
  authDomain: 'elite-academy-67a15.firebaseapp.com',
  projectId: 'elite-academy-67a15',
  storageBucket: 'elite-academy-67a15.firebasestorage.app',
  messagingSenderId: '53377882422',
  appId: '1:53377882422:web:dddcb2f63b4fcc089f7b97'
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage(function(payload) {
  console.log('[FCM SW] Received background message:', payload);
  const notificationTitle = payload.notification ? payload.notification.title : (payload.data ? payload.data.title : 'Elite Academy Notification');
  const notificationOptions = {
    body: payload.notification ? payload.notification.body : (payload.data ? payload.data.body : ''),
    icon: payload.notification ? payload.notification.image : '/images/logo.png',
    data: payload.data || {}
  };

  self.registration.showNotification(notificationTitle, notificationOptions);
});
