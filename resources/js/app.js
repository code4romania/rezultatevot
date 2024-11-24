import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import Clipboard from '@ryangjchandler/alpine-clipboard';
import embed from './embed.js';
import map from './map.js';

import.meta.glob(['../images/**']);

Alpine.plugin(Clipboard);
Alpine.data('embed', embed);
Alpine.data('map', map);

Livewire.start();
