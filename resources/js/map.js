import L from 'leaflet';

import 'leaflet/dist/leaflet.css';

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

const style = (feature) => ({
    weight: 2,
    opacity: 1,
    // color: 'white',
    fillOpacity: 0.5,
    // fillColor: getColor(feature.properties.density),
});

export default (url) => ({
    map: null,
    tooltip: L.tooltip(),

    init() {
        this.map = L.map(this.$el, {
            renderer: L.canvas(),
            zoomSnap: 0.1,
        });

        // this.initTooltip();

        this.geoJSON();
    },

    async geoJSON() {
        const geojson = L.geoJSON(await (await fetch(url)).json(), {
            style,
            onEachFeature: (feature, layer) => {
                layer.on({
                    mouseover: ({ target }) => {
                        target.setStyle({
                            weight: 5,

                            fillOpacity: 0.8,
                        });

                        target.bringToFront();
                    },
                    mouseout: ({ target }) => {
                        geojson.resetStyle(target);
                    },
                    click: () => {},
                });
            },
        })
            .bindTooltip((layer) => JSON.stringify(layer.feature.properties), {
                // sticky: true,
            })
            .addTo(this.map);
        this.map.fitBounds(geojson.getBounds(), {
            padding: [20, 20],
        });
    },

    initTooltip() {
        this.tooltip.onAdd = function (map) {
            this._div = L.DomUtil.create('div', 'info');
            this.update();
            return this._div;
        };

        this.tooltip.update = function (props) {
            console.log(props);
            const contents = props
                ? `<b>${props.name}</b><br />${props.density} people / mi<sup>2</sup>`
                : 'Hover over a state';
            this._div.innerHTML = `<h4>US Population Density</h4>${contents}`;
        };

        this.tooltip.addTo(this.map);
    },
});
