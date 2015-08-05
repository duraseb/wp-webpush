jQuery(function($){
    var $el = $('.webpush_subscribe_button');
    if (register_serviceworker()) {
        $el.click(function(e) {
            var text = $(this).text();
            if (text == $(this).data('subscribe')) {
                subscribe();
            } else if (text == $(this).data('unsubscribe')) {
                unsubscribe();
            }
        });
    }

    function show_buttons(subscribe) {
        $el.each(function(idx, el) {
            var $button = $(el);
            var button_text;
            if (!subscribe) {
                button_text = $button.data('unsubscribe');
            } else {
                button_text = $button.data('subscribe');
            }
            $button.text(button_text);
        });
    }

    function register_serviceworker() {
        if (typeof webpush_endpoint == 'string' && 'serviceWorker' in navigator) {
            navigator.serviceWorker.register(webpush_endpoint + '/?webpush_handler=serviceworker').then(initialiseState);
            return true;
          }
          console.warn("Service workers aren't supported in this browser.");
          return false;
    }

    function initialiseState(registration) {
        if (!('showNotification' in ServiceWorkerRegistration.prototype)) {
            console.warn("Notifications aren't supported.");
            return;
        }

        if (!('PushManager' in window)) {
            console.warn("Push messaging isn't supported.");
            return;
        }

        if (Notification.permission === 'denied') {
            show_buttons(true);
            $el.closest('.widget').show();
            console.warn("The user has blocked notifications.");
            return;
        }

        navigator.serviceWorker.ready.then(function(svWorker) {
            svWorker.pushManager.getSubscription().then(function(subscription) {
                if (subscription) {
                    show_buttons(false);
                    console.log('Subscription', subscription);
                } else {
                    show_buttons(true);
                }
                $el.closest('.widget').show();
            })
            .catch(function(err) {
                show_buttons(true);
                console.warn('Error during getSubscription()', err);
            });
        });

    }

    function sendSubscriptionToServer(action, subscription) {
        $.post(webpush_endpoint + '/?webpush_handler=subscribe', {
            action: action,
            subscription: JSON.stringify(subscription)
        });
    }

    function subscribe() {
        if (Notification.permission === 'denied') {
            alert('Please enable notifications permission in your browser settings');
        } else {
            navigator.serviceWorker.ready.then(function(serviceWorkerRegistration) {
                serviceWorkerRegistration.pushManager.subscribe({userVisibleOnly: true})
                .then(function(subscription) {
                    console.log('Subscribed', subscription);
                    sendSubscriptionToServer('subscribe', subscription);
                    show_buttons(false);
                    return;
                })
                .catch(function(e) {
                    if (Notification.permission === 'denied') {
                        console.warn('Permission for notifications was denied');
                    } else {
                        console.warn('Unable to subscribe to push. Permission: ' + Notification.permission, e);
                    }
                });
            });
        }
      }

    function unsubscribe() {
        navigator.serviceWorker.ready.then(function(serviceWorkerRegistration) {
            serviceWorkerRegistration.pushManager.getSubscription().then(
                function(subscription) {
                    if (!subscription) {
                        show_buttons(true);
                        return;
                    }
                    subscription.unsubscribe().then(function(successful) {
                        console.log('Unsubscribed', subscription);
                        sendSubscriptionToServer('unsubscribe', subscription);
                        show_buttons(true);
                    }).catch(function(e) {
                        console.warn('Unsubscribe error: ', e);
                    });
                }).catch(function(e) {
                    console.error('Error thrown while unsubscribing from push messaging.', e);
                });
            });
      }

});
