# Shortcode [servicekatalog]

Mithilfe des Shortcodes `[servicekatalog]` können die eingetragenen Services im WordPress-Content ausgegeben werden. Auswahl und Aussehen lassen sich durch folgende Shortcode-Attribute anpassen:

## Attribute

| Attribut     | Beschreibung                                                                                              | Standard | Mögliche Werte                                                                                                         |
|--------------|-----------------------------------------------------------------------------------------------------------|---------|------------------------------------------------------------------------------------------------------------------------|
| `display`    | Layout der Ausgabe                                                                                        | `grid`  | `grid`, `list`                                                                                                         |
| `searchform` | Formular zur Suche und Filter der Liste anzeigen                                                          |         | `1`                                                                                                                    |
| `commitment` | Filter nach einer oder mehreren Verbindlichkeiten (kommagetrennt)                                         |         | Ein oder mehrere Slugs der angelegten Verbindlichkeiten                                                                |
| `group`      | Filter nach einer oder mehreren Zielgruppen (kommagetrennt)                                               |         | Ein oder mehrere Slugs der angelegten Zielgruppen                                                                      |
| `tag`        | Filter nach einem oder mehreren Schlagworten (kommagetrennt)                                              |         | Ein oder mehrere Slugs der angelegten Schlagwörter                                                                     |
| `ids`        | Auswahl bestimmter Service-IDs                                                                            |         | Eine oder mehrere Service-IDs                                                                                          |
| `orderby`    | Reihenfolge der Ausgabe.<br /> Ist ein Service mehrern Taxonomien zugeordnet, erscheint er ggf. mehrfach. | `title` | `commitment`, `group`, `tag`                                                                                           |
| `hide`       | Liste der Elemente, die verborgen werden sollen                                                           |         | `thumbnail`, `commitment`, `group`, `tag`, `description`, `url-portal`, `url-description`, `url-tutorial`, `url-video` |


