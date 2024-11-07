import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import map from './map.js';

Alpine.data('map', map);

Livewire.start();
