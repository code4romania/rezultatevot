import L from 'leaflet';

import.meta.glob(
    [
        '../geojson/**/*.geojson',
        // Exclude source files
        '!../geojson/ro_*_poligon.geojson',
    ],
    {
        query: '?url',
    }
);

export default () => ({
    map: null,
    tooltip: L.tooltip(),
    isWorldMap: false,

    init() {
        this.isWorldMap = this.$wire.level === 'D';

        this.map = L.map(this.$el, {
            zoomControl: this.isWorldMap,
            dragging: this.isWorldMap,
            touchZoom: this.isWorldMap,
            scrollWheelZoom: this.isWorldMap,
            zoomSnap: this.isWorldMap ? 1.0 : 0.1,
            minZoom: 3,
            maxBoundsViscosity: 1.0,
            maxBounds: [
                [-90, -180],
                [90, 180],
            ],
        });

        this.geoJSON();
    },

    async geoJSON() {
        const geojson = L.geoJSON(await (await fetch(this.$el.dataset.url)).json(), {
            style: (feature) => ({
                weight: 1,
                opacity: 0.75,
                fillOpacity: 1,
                color: 'white',
                fillColor: this.$wire.data[feature.properties?.id]?.color || '#DDD',
            }),
            onEachFeature: (feature, layer) => {
                if (!feature.properties?.id) {
                    return;
                }

                layer.bindTooltip(
                    `<strong>${feature.properties.name}</strong><br/>
                        ${this.$wire.data[feature.properties.id]?.value || '&mdash;'}`,
                    {
                        sticky: true,
                        direction: 'top',
                    }
                );

                layer.on({
                    mouseover: ({ target }) => {
                        target.setStyle({
                            weight: 2,
                            opacity: 1,

                            fillOpacity: 0.8,
                        });

                        target.bringToFront();
                    },
                    mouseout: ({ target }) => {
                        geojson.resetStyle(target);
                    },
                    click: ({ target }) => {
                        console.log(target.feature.properties);

                        // Livewire.navigate(this.$wire.actionUrl);
                    },
                });
            },
        }).addTo(this.map);

        if (this.isWorldMap) {
            this.map.setView([45.9432, 24.9668], 3);
        } else {
            this.map.fitBounds(geojson.getBounds(), {
                padding: [20, 20],
            });
        }
    },
});
