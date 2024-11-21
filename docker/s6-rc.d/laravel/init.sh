#!/command/with-contenv sh

source /etc/profile.d/aliases.sh

artisan migrate --force
artisan config:cache
artisan event:cache
artisan route:cache
artisan view:cache
# artisan icons:cache # done by filament:optimize
artisan filament:optimize
artisan storage:link

artisan about
