# API (GARAN)

Felder: `eu_garan_enabled`, `eu_garan_years`, `eu_garan_brand`, `eu_garan_model`, `eu_garan_declaration`

Token: Magento-Integration, Recht `Solutioo_EuGuaranteeLabel::api`  
URL-Prefix: `/rest/all/V1`

```
GET  /solutioo/eu-guarantee/garan/product/:sku
PUT  /solutioo/eu-guarantee/garan/product/:sku
PUT  /solutioo/eu-guarantee/garan/products
POST /solutioo/eu-guarantee/garan/import/xml
```

GET Query optional: `?storeId=0` (0 = Default-Store)

---

### lesen

```bash
curl -H "Authorization: Bearer TOKEN" \
  "https://HOST/rest/all/V1/solutioo/eu-guarantee/garan/product/SKU"
```

Antwort z.B.:

```json
{
  "sku": "SKU",
  "store_id": 0,
  "enabled": true,
  "years": 5,
  "brand": "Marke",
  "model": "Modell",
  "declaration": "",
  "is_valid": true
}
```

`is_valid` = würde im Shop angezeigt (an + Jahre > 2 + Marke + Modell). SKU fehlt → 404.

---

### ein Produkt schreiben

Body-Key heißt `data` (Magento):

```bash
curl -X PUT -H "Authorization: Bearer TOKEN" -H "Content-Type: application/json" \
  -d '{"data":{"store_id":0,"enabled":true,"years":5,"brand":"Marke","model":"Modell","declaration":""}}' \
  "https://HOST/rest/all/V1/solutioo/eu-guarantee/garan/product/SKU"
```

Schreiben:
- an (`enabled=true`): Jahre > 2, brand + model Pflicht, sonst 400
- aus: Rest darf unvollständig sein
- `declaration` egal

---

### mehrere

```bash
curl -X PUT -H "Authorization: Bearer TOKEN" -H "Content-Type: application/json" \
  -d '{"items":[{"sku":"A","enabled":true,"years":5,"brand":"X","model":"Y"},{"sku":"B","enabled":true,"years":2,"brand":"X","model":"Y"}]}' \
  "https://HOST/rest/all/V1/solutioo/eu-guarantee/garan/products"
```

Antwort: `success_count`, `failed_count`, `errors[{sku,message}]` — einzelne Fehler stoppen den Rest nicht.

---

### XML

XML kommt als String im JSON:

```bash
curl -X POST -H "Authorization: Bearer TOKEN" -H "Content-Type: application/json" \
  -d '{"xml":"<?xml version=\"1.0\"?><garanProducts><product sku=\"SKU\" storeId=\"0\"><enabled>1</enabled><years>5</years><brand>Marke</brand><model>Modell</model><declaration></declaration></product></garanProducts>"}' \
  "https://HOST/rest/all/V1/solutioo/eu-guarantee/garan/import/xml"
```

Format:

```xml
<garanProducts>
  <product sku="SKU" storeId="0">
    <enabled>1</enabled>
    <years>5</years>
    <brand>Marke</brand>
    <model>Modell</model>
    <declaration></declaration>
  </product>
</garanProducts>
```

gleiche Regeln wie Bulk.
