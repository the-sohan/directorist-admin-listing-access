Directorist - Admin Listing Access 1.0.0

Temporary companion plugin for Directorist and Directorist Pricing Plans 4.x.
Install this ZIP through Plugins > Add New > Upload Plugin, then activate.
Keep Directorist and Directorist Pricing Plans active alongside it.

Published listings authored by a user with the Administrator role can display
their single-listing content, header, sidebar and images without an assigned
package. An assigned package continues to use the extension's existing rules.
Other authors and non-published listings retain the existing behavior.
Plan assignment remains available in the original extension.

No settings or database changes are made. Deactivate to restore normal behavior.
After the official Pricing Plans release includes the fix, deactivate and delete
this companion plugin.

Compatibility: targets the FormFields and PlanServiceProvider display callbacks
in the 4.x extension inspected locally. Earlier major versions are not supported.
Package presence follows directorist_get_listing_package(); assigned expired
packages are not independently reclassified by this plugin.
