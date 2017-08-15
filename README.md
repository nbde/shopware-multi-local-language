# shopware-multi-local-language

Shopware plugin to enable using different locals and fallback
localisations.

Result of a day @VIISON

## Hierarchischer Snippet-Manager
Aktuell gibt es in Shopware keine sinnvolle Fallback-Hierarchie zwischen Snippet Namespaces verschiedener Locales. Ist eine Locale ausgewählt, für die kein Snippet-Namespace existiert oder für die einzelne Snippets fehlen, werden die Fallbacks aus dem Smarty-Tag (z.B. “{s name=hello}Fallback{/s}”) beim Lesen ausgewertet und in die Datenbank importiert. Ein echtes Delegieren an allgemeinere Locales findet nicht statt, speziell bei Sublocales wie de_AT oder de_CH.

Um korrekt mit Sublocales umzugehen, soll eine Subklasse von Shopware_Components_Snippet_Manager entwickelt werden, die beim Laden von Snippets aus der Datenbank für jedes einzelne Snippet hierarchisch arbeitet, indem das Snippet in dieser Reihenfolge gesucht wird:

- In der exakten Benutzer-Locale (z.B. de_AT),
- in der Hauptlocale der Sprache (im Beispiel de_DE), dabei soll zur Vereinfachung angenommen werden, dass die Hauptlocale einer Sprache die Locale mit demselben ISO 639-1 code und der niedrigsten ID in s_core_locales ist,
- in der erstbesten anderen Locale derselben Sprache, falls das Snippet dort definiert ist,
- in der Hauptlocale für Englisch (en_GB),
- in einer beliebigen anderen Englisch-Locale.

