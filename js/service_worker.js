self.addEventListener('push', function(event) {
    console.log('Notification received', event);
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    if (clients.openWindow) {
        return clients.openWindow('/');
    }
  });

self.addEventListener('install', function(event) {
    console.log('Service Worker - installing');
    if ('replace' in event) {
        event.replace();
    } else {
        event.waitUntil(self.skipWaiting());
    }
});
