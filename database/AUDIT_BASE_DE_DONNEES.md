# Audit Architectural & Modélisation Relationnelle de la Base de Données — Employé API

> **Projet :** API SIRH, Carrières, Congés & Paie (`employe-api`)  
> **Chemin cible dans le projet :** `backends/employe-api/database/AUDIT_BASE_DE_DONNEES.md`  
> **Date de l'audit :** Novembre 2026  
> **Auteur :** Lead Database & SaaS Architect  
> **Statut du document :** Rapport de Référence Architectural  
> **SGBD Cible :** MySQL 8.0+ (Utilisation avancée de Window Functions)

---

## 1. Vue d'Ensemble & Domaine Métier

Le projet **employe-api** constitue le Système d'Information des Ressources Humaines (SIRH) de l'entreprise. Il gère l'ensemble du cycle de vie du collaborateur à travers plusieurs sous-domaines hautement interconnectés :
1. **Dossier Individuel & Référentiels :** Fiche employé (`employes`), titres/civilités (`titles`), classifications de la convention collective (`classifications`), postes budgétés (`positions`), unités organisationnelles et organigramme hiérarchique (`units`).
2. **Gestion de Carrière & Événements RH :** Modélisation unifiée des mouvements (`career_events`) spécialisés en affectations d'unités (`assignments`), promotions/reclassements (`promotions`), départs à la retraite (`retirements`), licenciements (`dismissals`), et sanctions disciplinaires (`sanctions`).
3. **Moteur d'Absences & Congés :** Types d'absence paramétrables (`absence_types`), crédits de congés périodiques idempotents (`leave_grants`), workflow de demandes et approbations (`absence_requests`), absences effectives (`absences`), jours fériés nationaux (`holidays`).
4. **Moteur de Paie & Contrats :** Types de contrats (`contract_types`), contrats de travail (`contracts`), grilles de rémunération (`classification_payroll_line_type`), bulletins de salaire (`payslips`) et lignes détaillées de rémunération/cotisations (`payslip_lines`).
5. **Gestion Électronique de Documents (GED) Polymorphique :** Pièces jointes et justificatifs (`attachments`).
6. **Vues Analytiques SQL :** Vues de calcul d'affectation courante (`current_assignments`), solde de congés (`leave_balances`), absences en cours (`ongoing_absences`), planning d'unité (`unit_absence_schedule`) et effectifs (`unit_staffing`).

---

## 2. Cartographie & Diagramme Relationnel (Entity-Relationship Diagram)

```mermaid
erDiagram
    TITLES ||--o{ EMPLOYES : "civilite"
    CLASSIFICATIONS ||--o{ EMPLOYES : "classe"
    POSITIONS ||--o{ EMPLOYES : "occupe"
    CLASSIFICATIONS ||--o{ POSITIONS : "grille"
    UNITS ||--o{ UNITS : "parent"
    
    EMPLOYES ||--o{ CAREER_EVENTS : "vit"
    CAREER_EVENTS ||--|| ASSIGNMENTS : "specialisation (CTI)"
    CAREER_EVENTS ||--|| PROMOTIONS : "specialisation (CTI)"
    CAREER_EVENTS ||--|| RETIREMENTS : "specialisation (CTI)"
    CAREER_EVENTS ||--|| DISMISSALS : "specialisation (CTI)"
    CAREER_EVENTS ||--|| SANCTIONS : "specialisation (CTI)"
    CAREER_EVENTS ||--|| ABSENCES : "specialisation (CTI)"
    
    UNITS ||--o{ ASSIGNMENTS : "affecte a"
    POSITIONS ||--o{ ASSIGNMENTS : "au poste de"
    
    ABSENCE_TYPES ||--o{ ABSENCES : "qualifie"
    ABSENCE_TYPES ||--o{ LEAVE_GRANTS : "credite"
    EMPLOYES ||--o{ LEAVE_GRANTS : "recoit"
    EMPLOYES ||--o{ ABSENCE_REQUESTS : "depose"
    EMPLOYES ||--o{ ABSENCE_REQUESTS : "valide"
    ABSENCE_TYPES ||--o{ ABSENCE_REQUESTS : "motif"
    ABSENCE_REQUESTS ||--o| ABSENCES : "aboutit a"
    
    EMPLOYES ||--o{ CONTRACTS : "signe"
    CONTRACT_TYPES ||--o{ CONTRACTS : "regit"
    CONTRACTS ||--o{ PAYSLIPS : "genere"
    EMPLOYES ||--o{ PAYSLIPS : "destine a"
    PAYSLIPS ||--o{ PAYSLIP_LINES : "detaille"
    PAYROLL_LINE_TYPES ||--o{ PAYSLIP_LINES : "associe a"
    CLASSIFICATIONS ||--o{ CLASSIFICATION_PAYROLL_LINE_TYPE : "definit grille"
    PAYROLL_LINE_TYPES ||--o{ CLASSIFICATION_PAYROLL_LINE_TYPE : "valeur grille"

    CAREER_EVENTS {
        bigint id PK
        bigint employee_id FK
        date event_date
        string event "Enum (assignment, promotion...)"
        bigint user_id "Operateur"
        datetime created_at
        datetime updated_at
    }

    ASSIGNMENTS {
        bigint id PK_FK "References career_events.id"
        bigint position_id FK
        bigint unit_id FK
        date start_date
        date end_date
        string reason
    }

    ABSENCES {
        bigint id PK_FK "References career_events.id"
        bigint absence_type_id FK
        bigint absence_request_id FK
        date start_date
        date end_date
        integer duration_days
        integer duration_hours
        boolean excess_is_deductible
        decimal deducted_days
    }

    CONTRACTS {
        bigint id PK
        bigint employee_id FK
        bigint contract_type_id FK
        date start_date
        date end_date
        string pay_frequency
        decimal base_salary
        string status
    }

    PAYSLIPS {
        bigint id PK
        bigint employee_id FK
        bigint contract_id FK
        string period "YYYY-MM"
        date issue_date
        decimal gross_salary
        decimal total_earnings
        decimal total_deductions
        decimal net_pay
        string status
    }
```

---

## 3. Points Forts de la Modélisation Actuelle

1. **Excellente Implémentation du Pattern Class Table Inheritance (CTI) :**  
   L'utilisation de `career_events` comme supertype central avec clé primaire partagée (`$table->foreignId('id')->primary()->constrained('career_events')->cascadeOnDelete()`) pour `assignments`, `promotions`, `retirements`, `dismissals`, `sanctions` et `absences` est une référence d'architecture relationnelle. Cela assure une stricte 3NF en évitant toute redondance d'`employee_id` et de date d'événement dans les sous-tables.
2. **Garantie d'Idempotence sur les Crédits de Congés :**  
   La table `leave_grants` intègre une clé unique composite d'idempotence :  
   `UNIQUE(employee_id, absence_type_id, period)`.  
   Ce choix élimine tout risque de double crédit lors du rejeu de jobs batch asynchrones ou de pannes temporaires de queue workers.
3. **Typage Monétaire Stricte à Haute Précision :**  
   Toutes les colonnes financières de salaires de base, gains, retenues et salaires nets utilisent `DECIMAL(15, 2)`, éliminant tout risque d'arrondis inexacts propres aux types à virgule flottante (`float`/`double`).
4. **Normalisation Avancée de la Grille Salariale :**  
   La table pivot `classification_payroll_line_type` adopte une véritable clé primaire composite `PRIMARY KEY (classification_id, payroll_line_type_id)` sans surrogate key superflue, optimisant l'arbre B-Tree InnoDB.
5. **Intégration de Vues Métier Performantes :**  
   La vue `current_assignments` résout le problème de l'affectation active avec partitionnement et fenêtrage SQL (`ROW_NUMBER() OVER (PARTITION BY ce.employee_id ORDER BY asg.start_date DESC)`), garantissant que la logique métier temporelle est encapsulée côté SGBD.

---

## 4. Anomalies, Risques et Points de Friction Identifiés

### Synthèse des Anomalies

| ID | Intitulé | Criticité | Table(s) Impactée(s) | Impact / Risque |
| :--- | :--- | :---: | :--- | :--- |
| **EMP-01** | Redondance d'état et Split-Brain : `position_id` et `classification_id` sur `employes` | **HAUTE** | `employes`, `assignments`, `promotions` | Désynchronisation entre le poste courant sur la fiche employé et l'historique des affectations |
| **EMP-02** | Risque de doublons de bulletin de paie sur une même période | **HAUTE** | `payslips` | Génération multiple de bulletins pour un même salarié sur le même mois |
| **EMP-03** | Typage flottant imprécis sur les indemnités de licenciement | **MOYENNE** | `dismissals` | Dérive de calculs comptables due au type `double` pour `severance_pay` |
| **EMP-04** | Clé d'audit non contrainte : `career_events.user_id` | **MOYENNE** | `career_events` | Clé orpheline sans contrainte ni indexation de recherche |
| **EMP-05** | Fragilité des tests d'intégration avec SQLite in-memory | **FAIBLE** | Vues SQL | Incompatibilité de certaines instructions SQL brutes lors des tests unitaires rapides |

---

### Analyse Détaillée des Anomalies

#### [EMP-01] Risque de Split-Brain : Dénormalisation sur `employes` (Criticité : HAUTE)
- **Constat :** La table `employes` contient `position_id` et `classification_id`.  
  Simultanément, les mouvements de postes sont consignés dans `assignments` (affectation à un poste dans une unité) et les évolutions de classification dans `promotions`.
- **Risque d'intégrité :**  
  Si un événement d'affectation est créé dans `assignments`, mais que l'attribut `employes.position_id` n'est pas mis à jour simultanément (ou si la mise à jour échoue hors transaction), l'employé aura deux postes différents selon la requête exécutée !
- **Recommandation 3NF :**  
  Dans une architecture 3NF pure, le poste courant et la classification courante doivent être résolus dynamiquement via la vue `current_assignments` ou un Scope Eloquent, plutôt que stockés en dur sur la table `employes`. Si le stockage dénormalisé est conservé pour des raisons de performance en lecture, il doit impérativement être protégé par un Observer/Trigger garantissant l'atomicité transactionnelle.

#### [EMP-02] Absence de contrainte unique composite sur `payslips` (Criticité : HAUTE)
- **Constat :** Dans la migration `create_payslips_table`, les colonnes sont :  
  `employee_id`, `contract_id`, `period (YYYY-MM)`, `issue_date`... sans contrainte unique composite.
- **Risque :** Un clic répété du gestionnaire RH ou un double déclenchement de commande CLI peut générer deux fiches de paie pour le même mois (`2026-11`) et le même contrat, faussant la comptabilité de l'entreprise.
- **Correction :** Poser la contrainte d'intégrité :  
  `$table->unique(['contract_id', 'period'], 'uq_payslips_contract_period');`

#### [EMP-03] Typage `double` sur `dismissals.severance_pay` (Criticité : MOYENNE)
- **Constat :** Migration `create_dismissals_table.php` :  
  `$table->double('severance_pay')->nullable();`
- **Risque :** Le standard monétaire du projet est `decimal(15, 2)`. L'utilisation de `double` (IEEE 754) introduit des erreurs de troncature et d'arrondis centésimaux lors de l'intégration dans les écritures de virement bancaire.
- **Correction :** Modifier la colonne en `decimal('severance_pay', 15, 2)->nullable()`.

#### [EMP-04] Audit non typé sur `career_events.user_id` (Criticité : MOYENNE)
- **Constat :** Dans `career_events`, la colonne `$table->bigInteger('user_id')->nullable();` n'a ni contrainte étrangère, ni index.
- **Risque :** Difficulté à retrouver tous les mouvements validés par un gestionnaire RH spécifique.

---

## 5. Recommandations Architecturales & Évolution 3NF

### Matrice d'Intégrité Cible

1. **Intégrité de la Paie :** Verrouiller l'unicité de la période par contrat (`contract_id`, `period`).
2. **Harmonisation Monétaire :** Convertir tous les champs financiers résiduels en `DECIMAL(15, 2)`.
3. **Indexation des Recherches Fréquentes :**
   - Absences par période : index composite `(absence_type_id, start_date, end_date)` sur `absences`.
   - Contrats actifs : index composite `(employee_id, status, start_date, end_date)` sur `contracts`.

---

## 6. Proposition Concrète de Migration de Refactoring

Fichier recommandé : `database/migrations/2026_11_07_000001_harden_employe_api_payroll_and_career.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Sécurise les contraintes uniques de paie et harmonise les types monétaires.
     */
    public function up(): void
    {
        // 1. Protection contre les doublons de bulletins de paie
        Schema::table('payslips', function (Blueprint $table) {
            $table->unique(['contract_id', 'period'], 'uq_payslips_contract_period');
            $table->index(['employee_id', 'period'], 'idx_payslips_employee_period');
        });

        // 2. Correction du type d'indemnité de licenciement en DECIMAL
        Schema::table('dismissals', function (Blueprint $table) {
            $table->decimal('severance_pay', 15, 2)->nullable()->change();
        });

        // 3. Indexation de la traçabilité des événements de carrière
        Schema::table('career_events', function (Blueprint $table) {
            $table->index(['employee_id', 'event_date'], 'idx_career_employee_date');
            $table->index('user_id', 'idx_career_events_operator');
        });

        // 4. Indexation optimisée pour le calcul des soldes d'absences
        Schema::table('absences', function (Blueprint $table) {
            $table->index(['absence_type_id', 'start_date', 'end_date'], 'idx_absences_type_dates');
        });
    }

    /**
     * Annule les modifications.
     */
    public function down(): void
    {
        Schema::table('absences', function (Blueprint $table) {
            $table->dropIndex('idx_absences_type_dates');
        });

        Schema::table('career_events', function (Blueprint $table) {
            $table->dropIndex('idx_career_events_operator');
            $table->dropIndex('idx_career_employee_date');
        });

        Schema::table('dismissals', function (Blueprint $table) {
            $table->double('severance_pay')->nullable()->change();
        });

        Schema::table('payslips', function (Blueprint $table) {
            $table->dropUnique('uq_payslips_contract_period');
            $table->dropIndex('idx_payslips_employee_period');
        });
    }
};
```
