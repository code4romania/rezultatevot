# Processing map data

## Romania

data source: [Limite județe poligon - geo-spatial.org](https://geo-spatial.org/vechi/download/romania-seturi-vectoriale#frontiera)

```
npx mapshaper ro_frontiera_poligon.geojson \
    -filter-fields id,name \
    -simplify dp interval=500 \
    -o precision=0.01 ./romania.geojson
```

## Counties

data source: [Limite județe poligon - geo-spatial.org](https://geo-spatial.org/vechi/download/romania-seturi-vectoriale#judete)

```
npx mapshaper ro_judete_poligon.geojson \
    -filter-fields name,mnemonic,countyCode \
    -rename-fields id=countyCode,code=mnemonic \
    -simplify dp interval=500 \
    -o precision=0.01 ./counties.geojson
```


## Localities

data source: [Limite UAT poligon - geo-spatial.org](https://geo-spatial.org/vechi/download/romania-seturi-vectoriale#uat)

```
npx mapshaper ro_uat_poligon.geojson \
    -split countyCode \
    -each 'natcode = parseInt(natcode)' \
    -filter-fields natcode,name \
    -rename-fields id=natcode \
    -simplify dp interval=200 \
    -o extension=geojson precision=0.0001 ./localities
```


### Additional resources
- [Mapshaper Command Reference](https://github.com/mbloch/mapshaper/wiki/Command-Reference)
