# Règle Obligatoire : Vérification de la Documentation via Context7 (`context7-doc-agent`)

Tu dois **obligatoirement et automatiquement** consulter la documentation officielle via le sous-agent **`context7-doc-agent`** (connecté au serveur MCP **Context7**) **avant toute écriture ou modification de code** (`write_file`, `edit_file`).

## Workflow obligatoire à chaque écriture de code

1. **Avant d'écrire ou modifier du code** :
   - Si le sous-agent `context7-doc-agent` n'est pas encore enregistré dans la conversation courante, crée-le d'abord via l'outil `define_subagent` avec :
     - `Name`: `"context7-doc-agent"`
     - `EnableMCPTools`: `true`
     - `EnableWriteTools`: `false`
   - Appelle **systématiquement** `invoke_subagent` (`TypeName: "context7-doc-agent"`) en lui précisant :
     - Le framework / package concerné et sa version dans le projet (ex: Laravel 12, PHP 8.2, PHPUnit 11).
     - Les classes, méthodes, façades ou fonctionnalités que tu vas utiliser dans ton code.
2. **Dans le sous-agent `context7-doc-agent`** :
   - Le sous-agent utilise les outils MCP de **Context7** (`resolve-library-id` puis `get-library-docs` / `query-docs`) pour extraire la documentation officielle à jour et vérifier les signatures, options et bonnes pratiques.
3. **Application dans le code** :
   - Attends la réponse de `context7-doc-agent` avant d'utiliser `write_file` ou `edit_file`.
   - Adapte systématiquement le code écrit aux recommandations de la documentation officielle retournée par `context7-doc-agent` ainsi qu'aux conventions internes du projet (`employe-api`).

