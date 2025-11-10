Endpoint actions/{entity}/edit.php (GET zeigt Formular, POST speichert).
Provider: getSingle + updateMethod (z. B. getSubject(id), updateSubject(id, fach)).
Tabelle: ✓ wird zu Link slug.edit.php?id=ID.
Gemeinsame Form-Komponente (nutzt übergebenes $entity + Felder).
Validierung + Redirect zurück zur Liste mit ?updated=1.
Optional: CSRF-Token (können wir nachziehen).