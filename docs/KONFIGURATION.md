# EU Gewährleistung / GARAN – Konfiguration für Einsteiger

Dieses Modul heißt **Solutioo_EuGuaranteeLabel**.  
Ziel: gesetzliches EU-Gewährleistungslabel + optional GARAN im Shop anzeigen.

> **Wichtig:** Das Modul ist die technische Umsetzung. Ob alles rechtlich reicht, entscheidet **Händler / Rechtsberatung** – nicht diese Anleitung.

Voraussetzung: Magento 2 und Modul **Solutioo_Base** sind schon da.

---

## 1) Modul installieren (einmalig)

1. Ordner legen nach:  
   `app/code/Solutioo/EuGuaranteeLabel/`
2. Im Shop-Root ausführen (PHP-Version anpassen, z. B. `php8.2`):

```bash
php bin/magento module:enable Solutioo_EuGuaranteeLabel
php bin/magento setup:upgrade --keep-generated
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy de_DE -f --area frontend
php bin/magento cache:flush
```

3. Admin neu laden.

Wenn der Shop danach kaputt aussieht: Static-Content nochmal deployen und Cache leeren (siehe oben).

---

## 2) Wo stelle ich etwas ein?

Im Admin:

**Solutioo → EU Gewährleistung / GARAN**  
(oder: Stores → Configuration → Solutioo → EU Gewährleistung / GARAN)

Drei Kästen:

| Kasten | Wofür |
|--------|--------|
| **Allgemein** | An/Aus, Sprache, Zusatz-Link |
| **Anzeige Gewährleistungslabel** | Footer, Warenkorb, Checkout, Infoseite, Texte |
| **GARAN-Label** | Haltbarkeitsgarantie am Produkt (optional) |

Oben rechts Scope wählen (Default / Website / Store View), dann speichern, dann Cache leeren.

---

## 3) Minimum – nur Gewährleistungslabel (empfohlen zuerst)

So sieht der Shop das **gesetzliche Label**, ohne GARAN-Produktdaten.

### Allgemein

| Feld | Was eintragen | Bedeutung in Klartext |
|------|----------------|------------------------|
| **Aktiviert** | Ja / Nein | Modul im Shop an oder aus |
| **Locale für Label-Asset** | Auto (Store-Locale) | Nimmt die Shop-Sprache (z. B. `de`). |
| **Feste Locale** | nur bei „Fest“ | z. B. immer `de`, egal welche Store View |
| **Zusatz-Info-URL** | **leer lassen** | Dann setzt das Modul automatisch den Your-Europe-Link (gleicher Sinn wie der QR-Code). Nur füllen, wenn du bewusst etwas anderes willst. |

### Anzeige Gewährleistungslabel

| Feld | Empfehlung |
|------|------------|
| **Im Footer zeigen** | Ja |
| **Footer-Darstellung** | **Dezent: Link in Footer-Leiste** (Standard, empfohlen) |
| **Im Warenkorb zeigen** | Ja |
| **Im Checkout zeigen** | Ja |
| **Eigene Infoseite aktiv** | Ja |
| **Footer-Überschrift** | z. B. `Gesetzliche Gewährleistung` (Linktext in der Fußleiste). Befüllter Wert überschreibt i18n. |
| **Warenkorb-/Checkout-Linktext** | z. B. `Ihre gesetzlichen Gewährleistungsrechte`. Befüllter Wert überschreibt i18n. |

**Footer-Darstellung – zwei Varianten:**

| Wert | Optik | Verhalten |
|------|--------|-----------|
| **Dezent** | Link in der Footer-Linkzeile (JS sucht z. B. `.bottom-inner > .links`; sonst Fallback-Leiste) | Klick → Infoseite mit vollem EU-Label |
| **Auffällig** | Eigener Hinweis-Streifen über dem Footer | Aufklappen zeigt das Label direkt im Footer |

Speichern → Cache flush.

### Was du dann sehen musst

1. **Footer** (jede Seite): dezenter Link „Gesetzliche Gewährleistung“ (oder Streifen).  
   Ohne passende Footer-Linkzeile erscheint automatisch eine kleine Fallback-Leiste.  
2. **Infoseite:** `https://DEINE-DOMAIN/eu-guarantee/notice`  
3. **Warenkorb:** Kurzlink **automatisch unter den Checkout-Buttons**
   (Modul-Plugin, kein Theme-Eingriff). Bei stark abweichendem Cart-Theme
   prüfen, ob `ul.checkout-methods-items` noch vorhanden ist.  
4. **Checkout (Onepage):** Kurzlink ist technisch aktiv, Platzierung aber nur
   grob oben im Content — bei Bedarf Theme/Layout nachziehen (Sidebar/Summary).

Wenn nichts sichtbar ist: **Aktiviert** auf Ja stellen und Cache leeren.

---

## 4) GARAN (nur wenn wirklich passend)

GARAN ist die **freiwillige / herstellerseitige Haltbarkeitsgarantie** – **nicht** die gesetzliche Gewährleistung.

Nur anzeigen, wenn **alles** stimmt:

- Hersteller-Garantie für die **gesamte Ware**
- **kostenlos** für den Kunden
- Dauer **länger als 2 Jahre**

Sonst: GARAN aus oder am Produkt nicht aktivieren.

### Modul-Config (GARAN-Kasten)

| Feld | Empfehlung |
|------|------------|
| **GARAN aktiv** | Ja nur wenn ihr echte Fälle habt, sonst Nein |
| **Auf Produktseite zeigen** | Ja |
| **Link in Bestellbestätigung** | Ja (siehe Mail unten) |

### Am Produkt (Attribute)

Im Produkt unter den Attributen (Namen sinngemäß):

| Attribut | Beispiel | Pflicht |
|----------|----------|---------|
| `eu_garan_enabled` | Ja | Ja |
| `eu_garan_years` | `5` (ganze Zahl **> 2**) | Ja |
| `eu_garan_brand` | Marke / Hersteller | Ja |
| `eu_garan_model` | Modellbezeichnung | Ja |
| `eu_garan_declaration` | CMS-ID, `media/...` oder https-URL zur Garantieerklärung | neat, empfohlen |

Speichern → Produktseite öffnen → unter dem Preis: kleines GARAN-Nested-Label → Klick öffnet volles Label mit Jahren/Marke/Modell.

Wenn nichts erscheint: Jahre ≤ 2, Feld leer, GARAN global aus, oder Cache.

---

## 5) Bestellbestätigungs-Mail (einmal pro Theme/Template)

Das Modul legt eine Variable bereit: **`solutioo_eu_garan_note`**  
(Inhalt nur, wenn im Auftrag GARAN-relevante Produkte sind und Mail-Option an ist.)

In der **Bestellbestätigungs**-Vorlage (Admin → Marketing → E-Mail-Vorlagen oder Theme-`*.html`) an sinnvoller Stelle einfügen:

```
{{var solutioo_eu_garan_note|raw}}
```

**Wichtig (Guidelines):** Die Variable oben deckt nur **GARAN** ab.  
Das **gesetzliche Notice-Label** gehört laut Guidelines **ebenfalls** in die Bestätigungsmail — das Modul fügt es **nicht** automatisch ein.  
Praktisch z. B.:

- Text + Link auf `https://DEINE-DOMAIN/eu-guarantee/notice`, oder  
- Notice-Grafik/HTML manuell in die Mail-Vorlage (mit Rechtsfreigabe).

Ohne diesen manuellen Mail-Schritt (GARAN-Variable + Notice) ist die Frontend-Pflicht oft schon gut bedient – die **Mail** aber noch nicht „fertig“.

---

## 7) Checkliste „fertig zum Live“

- [ ] Modul enabled, keine Fehler nach `setup:upgrade` / Deploy  
- [ ] Footer + Infoseite + Warenkorb geprüft (Checkout-Platzierung bewusst ansehen)  
- [ ] Your-Europe-Link im Footer-Panel öffnet (Zusatz-URL leer = automatisch)  
- [ ] GARAN nur an echten Produkten, sonst aus  
- [ ] Bestellmail: `{{var solutioo_eu_garan_note|raw}}` + gesetzlicher Notice-Hinweis  
- [ ] Rechtsfreigabe eingeholt  
- [ ] Cache / Varnish / FPC nach Config geleert  

---

## 8) Häufige Probleme

| Problem | Typische Ursache |
|---------|------------------|
| Nichts im Footer | Modul aus, Anzeige „Footer“ aus, Cache; bei „Dezent“ ohne Theme-Linkzeile: Fallback-Leiste prüfen |
| Infoseite 404 | Cache / Rewrite; URL genau: `/eu-guarantee/notice` |
| Falsche Sprache im Label | Locale-Modus / Store-View-Sprache; oder feste Locale setzen |
| GARAN fehlt am Produkt | Attribute unvollständig oder Jahre nicht > 2 |
| GARAN-Bild zeigt noch „XX“ | Cache; Bild-URL muss `/eu-guarantee/garan/image/` nutzen |
| CSS wirkt nicht | `setup:static-content:deploy` + Cache |

---

## 9) Was das Modul **nicht** ist

- Kein Ersatz für AGB / Widerruf / Impressum  
- Keine automatische Rechtsprüfung  
- Läuft in jedem Magento 2 mit **Solutioo_Base** (kein Shop-/Theme-Zwang)
- Warenkorb-Kurzlink ohne Theme-Patch (Plugin nach den CTAs)
- Keine magische Mail-Vorlage für jedes Theme

Weitere Technik/Guidelines: `README.md`, `docs/GUIDELINES-NOTES.md`, PDF in `docs/`.
