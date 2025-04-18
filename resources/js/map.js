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

    return value !== '' && value !== null;
};

export default () => ({
    map: null,
    tooltip: L.tooltip(),
    isWorldMap: false,
    layer: null,

    init() {
        this.isWorldMap = this.$wire.level === 'D';

        this.map = L.map(this.$el, {
            zoomControl: this.isWorldMap,
            dragging: this.isWorldMap,
            touchZoom: this.isWorldMap,
            scrollWheelZoom: false,
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

        this.legend(this.$wire.legend);
    },

    async geoJSON() {
        this.layer = L.geoJSON(await (await fetch(this.$el.dataset.url)).json(), {
            attribution: `Sursă date: <a href="https://www.roaep.ro/" rel="noopener">Autoritatea Electorală Permanentă</a> | GeoJSON: <a href="https://geo-spatial.org/vechi/download/romania-seturi-vectoriale" rel="noopener">geo-spatial.org</a>`,
            style: (feature) => {
                const data = this.$wire.data[feature.properties?.id];
                const style = {
                    weight: 1,
                    opacity: 1,
                    fillOpacity: 1,
                    color: '#FFF',
                    className: 'fill-gray-300',
                };

                if (!hasValue(data)) {
                    return style;
                }

                if (hasValue(data.class)) {
                    style.className = data.class;
                } else if (hasValue(data.value) && hasValue(data?.color)) {
                    delete style.className;
                    style.fillColor = data.color;
                }

                return style;
            },
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
                        this.layer.resetStyle(target);
                    },
                    click: ({ target }) => {
                        if (this.$wire.embed) {
                            return;
                        }

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

        this.fitBounds();
    },

    legend(scale) {
        if (!hasValue(scale) || !Array.isArray(scale)) {
            return;
        }

        const legend = L.control({
            position: 'bottomright',
        });

        legend.onAdd = function (map) {
            const div = L.DomUtil.create('ol', 'bg-white py-2 px-2 rounded drop-shadow leading-4 pointer-events-none');

            for (const step of scale) {
                div.innerHTML += `<li class="flex gap-1 items-center">
                    <i class="w-4 h-4 inline-flex ${step.color}"></i>
                    <span>${step.label}</span>
                </li>`;
            }

            return div;
        };

        legend.addTo(this.map);
    },

    fitBounds() {
        if (this.isWorldMap) {
            this.map.setView([45.9432, 24.9668], 3);
        } else if (this.layer) {
            this.map.fitBounds(this.layer.getBounds(), {
                padding: [20, 20],
            });
        }
    },

    resize() {
        this.$nextTick(() => {
            if (!this.isWorldMap) {
                this.fitBounds();
            }
        });
    },
});
