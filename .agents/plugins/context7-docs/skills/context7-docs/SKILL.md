---
name: context7-docs
description: >-
  Procédure pour vérifier automatiquement la documentation officielle à jour via le serveur MCP Context7
  et le sous-agent context7-doc-agent avant toute écriture ou modification de code.
---

# Consultation Automatique de la Documentation (Context7 MCP)

Cette compétence garantit que tout code produit ou modifié respecte strictement la documentation officielle à jour des frameworks et librairies du projet via **Context7 MCP**.

## Quand activer cette procédure
- **Systématiquement avant d'écrire, générer ou modifier du code** (`write_file`, `edit_file`) faisant appel à un framework ou une librairie (Laravel 12, Eloquent, Middlewares, Validation, Tests PHPUnit, etc.).

## Étapes à suivre

1. **S'assurer que le sous-agent `context7-doc-agent` est disponible** :
   - Si `context7-doc-agent` n'est pas encore défini dans la session courante, définis-le avec `define_subagent` (`Name: "context7-doc-agent"`, `EnableMCPTools: true`, `EnableWriteTools: false`).
2. **Invoquer `context7-doc-agent` via `invoke_subagent`** :
   - Passe-lui une description précise du code que tu t'apprêtes à écrire et des fonctionnalités/classes concernées.
   - Le sous-agent interroge Context7 MCP (`resolve-library-id` puis `get-library-docs` / `query-docs`) et te renvoie les signatures et bonnes pratiques officielles.
3. **Écrire le code en conformité avec la documentation** :
   - Applique les recommandations retournées par `context7-doc-agent` tout en respectant l'architecture existante du projet (`IModel`, `Repository`, `CheckAbility`, `VerifyAuthApiToken`).

