# Directorist Admin Listing Access

A temporary companion plugin for Directorist Pricing Plans 4.x. Published listings authored by an Administrator can display their content, header, sidebar and images when no pricing package is assigned.

Existing pricing rules remain in place for other authors and listings with an assigned package. The plugin does not edit the main extension's files or write settings to the database.

## Installation

Download `directorist-admin-listing-access-1.0.0.zip` from this repository's Releases page, upload it through WordPress **Plugins → Add New → Upload Plugin**, and activate it. Keep Directorist and Directorist Pricing Plans active.

Once the official Pricing Plans release includes this fix, deactivate and delete this companion plugin.

## Compatibility and verification

- Requires WordPress 6.0+ and PHP 7.4+.
- Targets the `FormFields` and `PlanServiceProvider` display callbacks in the inspected Pricing Plans 4.x code.
- Package presence follows `directorist_get_listing_package()`. Assigned expired packages are not independently reclassified.
- PHP syntax and 50 checks using WordPress's hook system passed; provider callbacks and listing records in those checks were test doubles.
- Browser verification was unavailable because the local site returned HTTP 502. End-to-end compatibility has not been verified.

License: GPL-3.0-or-later.
