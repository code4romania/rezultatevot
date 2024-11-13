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

const hasValue = (value) => {
    if (typeof value === 'undefined') {
        return false;
    }

    return value !== '' && value != 0 && value !== null;
};

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
            doubleClickZoom: this.isWorldMap,
            zoomSnap: this.isWorldMap ? 1.0 : 0.1,
            minZoom: 3,
            maxBoundsViscosity: 1.0,
            maxBounds: [
                [-90, -180],
                [90, 180],
            ],
            keyboard: false,
        });

        this.geoJSON();
    },

    async geoJSON() {
        const geojson = L.geoJSON(await (await fetch(this.$el.dataset.url)).json(), {
            style: (feature) => ({
                weight: 1,
                opacity: 1,
                fillOpacity: 1,
                color: 'white',
                fillColor: hasValue(this.$wire.data[feature.properties?.id]?.value)
                    ? this.$wire.data[feature.properties?.id]?.color || '#DDD'
                    : '#DDD',
            }),
            onEachFeature: (feature, layer) => {
                if (
                    !feature.properties?.id ||
                    feature.properties?.interactive === false ||
                    !hasValue(this.$wire.data[feature.properties.id]?.value)
                ) {
                    return;
                }

                let content = hasValue(this.$wire.data[feature.properties.id]?.percent)
                    ? this.$wire.data[feature.properties.id].percent
                    : this.$wire.data[feature.properties.id].value;

                if (hasValue(this.$wire.data[feature.properties.id]?.label)) {
                    content = `${this.$wire.data[feature.properties.id].label}: ${content}`;
                }

                layer.bindTooltip(`<strong>${feature.properties.name}</strong><br/>${content}`, {
                    sticky: true,
                    direction: 'top',
                });

                layer.on({
                    mouseover: ({ target }) => {
                        target.setStyle({
                            weight: 2,
                            fillOpacity: 0.75,
                        });

                        target.bringToFront();
                    },
                    mouseout: ({ target }) => {
                        geojson.resetStyle(target);
                    },
                    click: ({ target }) => {
                        if (this.$wire.level === 'D') {
                            this.$dispatch('map:click', { country: target.feature.properties.id });
                        }

                        if (this.$wire.level === 'N') {
                            if (this.$wire.county) {
                                this.$dispatch('map:click', { locality: target.feature.properties.id });
                            } else {
                                this.$dispatch('map:click', { county: target.feature.properties.id });
                            }
                        }
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
