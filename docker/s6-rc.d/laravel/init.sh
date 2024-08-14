#!/command/with-contenv sh

source /etc/profile.d/aliases.sh

artisan migrate --force
artisan optimize
artisan filament:optimize
artisan storage:link

artisan about
