# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- TMS-96: Blog comment author #120
- TMS-130: Forms #127
- TMS-204: Increase menu_order default value #146
- TMS-133: Add filters for share button & breadcrumb customization #141
- TMS-141: Add filters to Call to Action layout #142
- TMS-113: Contacts template #139
- TMS-192-1: Add filters for assets #137
- TMS-127: Filter to allowed blocks #133
- TMS-112: Contacts ACF block #110
- DR-5: Add Default Content Image setting #134

### Changed
- TMS-194: Organize setting tabs #135

### Fixed
- a11y: Fix navigation item duplicate ids #122
- a11y: Skip to content link #123
- a11y: Prevent duplicate img ids #124
- a11y: Move ul role attribute to a wrapping div #125
- a11y: Fix nav element hierarchy #126
- DR-1: misc appearance fixes and improvements #129
- DR-2: misc appearance fixes and improvements #130
- DR-3: misc appearance fixes and improvements #131
- DR-4: misc appearance fixes and improvements #132
- Fixes type error: expected array, got bool #138

## [1.7.0] - 2021-08-06

### Added

- TMS-55: Onepager page template #108

### Fixed
- Upgrade deprecated hooks #121

## [1.6.1] - 2021-08-03

### Fixed
- Remove character limit when displaying excerpts #119

## [1.6.0] - 2021-08-02

### Added
- TMS-114: Contact CPT #105

## [1.5.1] - 2021-08-02

### Fixed

- Compile potfile #118
- Fix blog-article related posts query taxonomy #117

## [1.5.0] - 2021-07-01

### Added

- TMS-79: Blog Article CPT #83
- TMS-78: Blog Article Archive #103
- TMS-80: Blog Article Authors #104
- TMS-81: Layout BlogArticles #107
- TMS-125: Accent color #106

### Changed

- NPM(deps-dev): Bump cross-env from 5.2.1 to 7.0.3 #102

## [1.4.0] - 2021-06-29

### Added

- TMS-82: Roles and Capabilities #61
- TMS-116: Add Notice Banner layout #100
- TMS-117: Map block #97
- TMS-84: Events calendar template #90
- TMS-74: Events layout #80
- TMS-109: Color theme selection #87
- TMS-76: Event view #81
- TMS-72: Dynamic Event #82
- TMS-59: Sitemap #84

### Changed

- TMS-97: Color themes part 2 #89
- TMS-97: Color themes #85
- NPM(deps-dev): Bump file-loader from 3.0.1 to 6.2.0 #5
- NPM(deps-dev): Bump style-loader from 0.23.1 to 2.0.0 #46
- NPM(deps-dev): Bump eslint-loader from 2.2.1 to 4.0.2 #49
- NPM(deps-dev): Bump webpack-cli from 3.3.12 to 4.7.2 #62
- NPM(deps-dev): Bump @babel/register from 7.13.14 to 7.14.5 #92
- NPM(deps-dev): Bump clean-webpack-plugin from 2.0.2 to 3.0.0 #93
- NPM(deps-dev): Bump bulma from 0.9.2 to 0.9.3 #95

### Fixed

- TMS-102-4: Allow empty value on highlight article field #101
- TMS-102-4: Remove character limit when displaying excerpts #101
- TMS-102-3: Fix sibling navigation href #91
- Fixes to codestyle, texts, colors and spacings #88
- TMS-97: Fix button styles #86

## [1.3.3] - 2021-06-21

### Added

- TMS-106: Add page hero #75
- TMS-95: Add header exception notice #78
- TMS-94: Add layout Articles #77

## [1.3.2] - 2021-06-17

### Fixed

- TMS-102-2: Hide menu toggle if navigation does not exist #76
- TMS-102-1: Add screen reader text to menu toggle #74
- TMS-102-1: Fix quote align #74
- Fixed cross-origin asset loading, added .no-hyphens

## [1.3.1] - 2021-06-16

### Changed

- Removed home icon from breadcrumbs #72

### Fixed

- TMS-102: Add check Display Search setting #71

## [1.3.0] - 2021-06-16

### Added

- TMS-102-editor: Disable Gutenberg colors and fonts #70
- TMS-102-editor: Hide custom links from navigation #70
- TMS-102-editor: Disable drop cap #70
- TMS-101: Setting to hide search #66

### Changed

- TMS-97: Generate multiple themes #68

## [1.2.0] - 2021-06-15

### Added

- TMS-100: Add Finnish translations #65
- TMS-83: Add sibling page list #64
- TMS-62: Add block Notice Banner #63
- TMS-42: Image Carousel #42
- TMS-53: Breadcrumbs #52

## [1.1.0] - 2021-06-07

### Added

- TMS-52: Header #59
- TMS-89: Add layout Grid #60
- TMS-68-1: Add archive / home view #58
- TMS-67-1: Add single view #57
- TMS-66: Add article fields #56
- TMS-57: Materials Page #55
- TMS-61: Subpages block & layout #54
- TMS-58: Error 404 page #53
- TMS-73: Text block (Layout) #51
- TMS-75: Text Editor #50

## [1.0.0] - 2021-05-21

### Added

- TMS-37: Social Media layout #44
- TMS-40-rework: Grid fixes, cleanup #36
- TMS-28-rework: Logo Wall spacings and hyphenation #37
- TMS-32-files: Allow template to be overridden from plugins #41
- TMS-33-icons: Add icons to IconLInks Layout #43
- TMS-65: Hero Layout #34
- TMS-55: Footer #38
- TMS-43: related posts for single view #27
- TMS-41: Image Gallery #30
- TMS-29: Image #26
- TMS-45: Map Block #23
- TMS-30: Video Block #22
- TMS-28: Logo wall #21
- TMS-39: Content Columns Layout #20
- TMS-32: Accordion Block #17
- TMS-40: Grid #18
- TMS-48: Colors, button style and cleanup #16
- TMS-48: Quote ACF Block #15
- TMS-48: Typography and Colors #12
- TMS-36: Link List ACF block #13
- TMS-27: Call to Action ACF layout #10
- TMS-38: Key Figures ACF block #9
- TMS-46: Image Banner #8
- feat/pr-automation-linting-and-tweaks #2
    - `.github/` directory with templates and pr automation
    - `.nvmrc` v14 (the current LTS version of npm)
    - Included correct `LICENSE` (was MIT, now GPL-3-or-later)

### Changed

- feat/pr-automation-linting-and-tweaks #2
    - `composer.json` minimum stability to `stable`

## 0.1.0 2021-04-19

### Added
- TMS-48: Theme boilerplate
