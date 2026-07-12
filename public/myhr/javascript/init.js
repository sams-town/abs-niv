// Unregister semua service worker lama (termasuk dari domain hris.rejofarm.com)
// agar tidak ada notifikasi push yang datang dari sistem lama
if ("serviceWorker" in navigator) {
    navigator.serviceWorker.getRegistrations().then(function(registrations) {
        for (let registration of registrations) {
            registration.unregister();
            console.log("Service worker unregistered:", registration.scope);
        }
    });
}
