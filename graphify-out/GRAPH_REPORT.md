# Graph Report - hello-elementor-child  (2026-05-02)

## Corpus Check
- 158 files · ~139,382 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 714 nodes · 931 edges · 22 communities detected
- Extraction: 97% EXTRACTED · 3% INFERRED · 0% AMBIGUOUS · INFERRED: 28 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Community Hubs (Navigation)
- [[_COMMUNITY_Community 0|Community 0]]
- [[_COMMUNITY_Community 1|Community 1]]
- [[_COMMUNITY_Community 2|Community 2]]
- [[_COMMUNITY_Community 3|Community 3]]
- [[_COMMUNITY_Community 4|Community 4]]
- [[_COMMUNITY_Community 5|Community 5]]
- [[_COMMUNITY_Community 6|Community 6]]
- [[_COMMUNITY_Community 7|Community 7]]
- [[_COMMUNITY_Community 8|Community 8]]
- [[_COMMUNITY_Community 9|Community 9]]
- [[_COMMUNITY_Community 10|Community 10]]
- [[_COMMUNITY_Community 11|Community 11]]
- [[_COMMUNITY_Community 12|Community 12]]
- [[_COMMUNITY_Community 14|Community 14]]
- [[_COMMUNITY_Community 15|Community 15]]
- [[_COMMUNITY_Community 16|Community 16]]
- [[_COMMUNITY_Community 18|Community 18]]
- [[_COMMUNITY_Community 19|Community 19]]
- [[_COMMUNITY_Community 24|Community 24]]
- [[_COMMUNITY_Community 25|Community 25]]
- [[_COMMUNITY_Community 31|Community 31]]
- [[_COMMUNITY_Community 32|Community 32]]

## God Nodes (most connected - your core abstractions)
1. `PHPMailer` - 125 edges
2. `SMTP` - 43 edges
3. `ClassLoader` - 26 edges
4. `Talent_Edit_Controller` - 22 edges
5. `Talent_Submission_Controller` - 18 edges
6. `RoleManager` - 16 edges
7. `InstalledVersions` - 16 edges
8. `POP3` - 12 edges
9. `velvet_reel_validate_portfolio_upload_groups()` - 8 edges
10. `velvet_reel_handle_portfolio_uploads()` - 8 edges

## Surprising Connections (you probably didn't know these)
- `handle_classified_create_redirect()` --calls--> `ensure_user_plan_tracking()`  [INFERRED]
  functions.php → includes/access-control.php
- `handle_classified_create_redirect()` --calls--> `migrate_user_current_level_to_plans()`  [INFERRED]
  functions.php → includes/access-control.php
- `handle_classified_create_redirect()` --calls--> `has_membership_plan()`  [INFERRED]
  functions.php → includes/access-control.php
- `wp_ajax_save_talent_progress()` --calls--> `velvet_reel_validate_portfolio_upload_groups()`  [INFERRED]
  functions.php → includes/talent-forms.php
- `wp_ajax_save_talent_progress()` --calls--> `velvet_reel_media_handle_upload()`  [INFERRED]
  functions.php → includes/talent-forms.php

## Communities

### Community 0 - "Community 0"
Cohesion: 0.03
Nodes (1): PHPMailer

### Community 1 - "Community 1"
Cohesion: 0.06
Nodes (45): addVideoLinkField(), buildDraftSaveRequestData(), buildPortfolioFileList(), buildPortfolioSaveMessage(), createDynamicRoleFields(), ensurePortfolioFeedbackElements(), filterRolesByDomain(), getPendingPortfolioUploadCount() (+37 more)

### Community 2 - "Community 2"
Cohesion: 0.05
Nodes (9): classified_page_flush_rewrite_rules(), create_advertisement_submission_page(), create_classified_pricing_page(), handle_stripe_webhook(), manually_update_talent_payment_status(), process_stripe_payment_confirmation(), record_talent_approval_payment(), trigger_talent_approval_payment_check() (+1 more)

### Community 3 - "Community 3"
Cohesion: 0.05
Nodes (2): ClassLoader, InstalledVersions

### Community 4 - "Community 4"
Cohesion: 0.1
Nodes (1): SMTP

### Community 5 - "Community 5"
Cohesion: 0.08
Nodes (8): handle_classified_create_redirect(), add_membership_plan(), ensure_user_plan_tracking(), handle_membership_level_change(), has_membership_plan(), migrate_user_current_level_to_plans(), ajax_express_advertisement_interest(), Talent_Submission_Controller

### Community 6 - "Community 6"
Cohesion: 0.06
Nodes (2): enqueue_talent_form_assets(), get_user_draft_profile()

### Community 7 - "Community 7"
Cohesion: 0.13
Nodes (1): Talent_Edit_Controller

### Community 8 - "Community 8"
Cohesion: 0.22
Nodes (15): handleFilePreview(), initExpressInterest(), initFileUploadPreviews(), initFormEnhancements(), initGlobalComponents(), initializeGlobalScripts(), initImageProtection(), initMobileMenuToggle() (+7 more)

### Community 9 - "Community 9"
Cohesion: 0.18
Nodes (1): RoleManager

### Community 10 - "Community 10"
Cohesion: 0.33
Nodes (13): wp_ajax_save_talent_progress(), handle_talent_submission(), handle_talent_update(), velvet_reel_format_portfolio_validation_error(), velvet_reel_get_first_portfolio_image_id(), velvet_reel_get_portfolio_upload_groups(), velvet_reel_get_portfolio_upload_rules(), velvet_reel_get_upload_error_message() (+5 more)

### Community 11 - "Community 11"
Cohesion: 0.22
Nodes (10): get_domain_role(), get_domains(), render_domain_cards(), render_role_cards(), render_all_role_fields_clean(), render_role_fields_consistent(), get_domain_roles(), get_role_fields_template_path() (+2 more)

### Community 12 - "Community 12"
Cohesion: 0.35
Nodes (1): POP3

### Community 14 - "Community 14"
Cohesion: 0.22
Nodes (2): add_advertisement_interests_menu(), get_advertisements_with_interests_count()

### Community 15 - "Community 15"
Cohesion: 0.28
Nodes (4): draft_submissions_page_callback(), get_draft_talent_submissions(), save_current_step_to_draft(), update_talent_current_step()

### Community 16 - "Community 16"
Cohesion: 0.39
Nodes (1): DSNConfigurator

### Community 18 - "Community 18"
Cohesion: 0.47
Nodes (1): OAuth

### Community 19 - "Community 19"
Cohesion: 0.4
Nodes (1): TalentFormState

### Community 24 - "Community 24"
Cohesion: 0.5
Nodes (2): include_portfolio_gallery_component(), render_generic_role_fields()

### Community 25 - "Community 25"
Cohesion: 0.5
Nodes (1): ComposerAutoloaderInit2185d2f99bcd56787481d9357a5972d3

### Community 31 - "Community 31"
Cohesion: 0.67
Nodes (1): Exception

### Community 32 - "Community 32"
Cohesion: 0.67
Nodes (1): ComposerStaticInit2185d2f99bcd56787481d9357a5972d3

## Knowledge Gaps
- **Thin community `Community 0`** (126 nodes): `PHPMailer`, `.addAddress()`, `.addAnAddress()`, `.addAttachment()`, `.addBCC()`, `.addCC()`, `.addCustomHeader()`, `.addEmbeddedImage()`, `.addOrEnqueueAnAddress()`, `.addrAppend()`, `.addReplyTo()`, `.addressHasUnicodeLocalPart()`, `.addrFormat()`, `.addStringAttachment()`, `.addStringEmbeddedImage()`, `.alternativeExists()`, `.anyAddressHasUnicodeLocalPart()`, `.attachAll()`, `.attachmentExists()`, `.base64EncodeWrapMB()`, `.cidExists()`, `.clearAddresses()`, `.clearAllRecipients()`, `.clearAttachments()`, `.clearBCCs()`, `.clearCCs()`, `.clearCustomHeader()`, `.clearCustomHeaders()`, `.clearQueuedAddresses()`, `.clearReplyTos()`, `.__construct()`, `.createBody()`, `.createHeader()`, `.__destruct()`, `.DKIM_Add()`, `.DKIM_BodyC()`, `.DKIM_HeaderC()`, `.DKIM_QP()`, `.DKIM_Sign()`, `.doCallback()`, `.edebug()`, `.encodeFile()`, `.encodeHeader()`, `.encodeQ()`, `.encodeQP()`, `.encodeString()`, `.endBoundary()`, `.fileIsAccessible()`, `.filenameToType()`, `.generateId()`, `.getAllRecipientAddresses()`, `.getAttachments()`, `.getBccAddresses()`, `.getBoundaries()`, `.getBoundary()`, `.getCcAddresses()`, `.getCustomHeaders()`, `.getLastMessageID()`, `.getLE()`, `.getMailMIME()`, `.getOAuth()`, `.getReplyToAddresses()`, `.getSentMIMEMessage()`, `.getSmtpErrorMessage()`, `.getSMTPInstance()`, `.getSMTPXclientAttributes()`, `.getToAddresses()`, `.getTranslations()`, `.has8bitChars()`, `.hasLineLongerThanMax()`, `.hasMultiBytes()`, `.headerLine()`, `.html2text()`, `.idnSupported()`, `.inlineImageExists()`, `.isError()`, `.isHTML()`, `.isMail()`, `.isPermittedPath()`, `.isQmail()`, `.isSendmail()`, `.isShellSafe()`, `.isSMTP()`, `.isValidHost()`, `.lang()`, `.mailPassthru()`, `.mailSend()`, `.mb_pathinfo()`, `._mime_types()`, `.msgHTML()`, `.needsSMTPUTF8()`, `.normalizeBreaks()`, `.parseAddresses()`, `.postSend()`, `.preSend()`, `.punyencodeAddress()`, `.quotedString()`, `.replaceCustomHeader()`, `.rfcDate()`, `.secureHeader()`, `.send()`, `.sendmailSend()`, `.serverHostname()`, `.set()`, `.setBoundaries()`, `.setError()`, `.setFrom()`, `.setLanguage()`, `.setLE()`, `.setMessageType()`, `.setOAuth()`, `.setSMTPInstance()`, `.setSMTPXclientAttribute()`, `.setWordWrap()`, `.sign()`, `.smtpClose()`, `.smtpConnect()`, `.smtpSend()`, `.stripTrailingBreaks()`, `.stripTrailingWSP()`, `.textLine()`, `.utf8CharBoundary()`, `.validateAddress()`, `.validateEncoding()`, `.wrapText()`, `PHPMailer.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 3`** (43 nodes): `ClassLoader`, `.add()`, `.addClassMap()`, `.addPsr4()`, `.__construct()`, `.findFile()`, `.findFileWithExtension()`, `.getApcuPrefix()`, `.getClassMap()`, `.getFallbackDirs()`, `.getFallbackDirsPsr4()`, `.getPrefixes()`, `.getPrefixesPsr4()`, `.getRegisteredLoaders()`, `.getUseIncludePath()`, `.initializeIncludeClosure()`, `.isClassMapAuthoritative()`, `.loadClass()`, `.register()`, `.set()`, `.setApcuPrefix()`, `.setClassMapAuthoritative()`, `.setPsr4()`, `.setUseIncludePath()`, `.unregister()`, `InstalledVersions`, `.getAllRawData()`, `.getInstalled()`, `.getInstalledPackages()`, `.getInstalledPackagesByType()`, `.getInstallPath()`, `.getPrettyVersion()`, `.getRawData()`, `.getReference()`, `.getRootPackage()`, `.getSelfDir()`, `.getVersion()`, `.getVersionRanges()`, `.isInstalled()`, `.reload()`, `.satisfies()`, `ClassLoader.php`, `InstalledVersions.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 4`** (42 nodes): `SMTP`, `.authenticate()`, `.client_send()`, `.close()`, `.connect()`, `.connected()`, `.data()`, `.edebug()`, `.errorHandler()`, `.get_lines()`, `.getDebugLevel()`, `.getDebugOutput()`, `.getError()`, `.getLastReply()`, `.getLastTransactionID()`, `.getServerExt()`, `.getServerExtList()`, `.getSMTPConnection()`, `.getSMTPUTF8()`, `.getTimeout()`, `.getVerp()`, `.hello()`, `.hmac()`, `.mail()`, `.noop()`, `.parseHelloFields()`, `.quit()`, `.recipient()`, `.recordLastTransactionID()`, `.sendAndMail()`, `.sendCommand()`, `.sendHello()`, `.setDebugLevel()`, `.setDebugOutput()`, `.setError()`, `.setSMTPUTF8()`, `.setVerp()`, `.startTLS()`, `.turn()`, `.verify()`, `.xclient()`, `SMTP.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 6`** (36 nodes): `functions-original.php`, `add_talent_custom_fields_to_elementor()`, `add_talent_taxonomy_support()`, `create_talent_country_taxonomy()`, `create_talent_cpt()`, `create_talent_state_taxonomy()`, `create_talent_tagging_taxonomy()`, `create_talent_taxonomy()`, `create_userinformation_table()`, `custom_enqueue_scripts()`, `custom_lostpassword_url()`, `custom_woocommerce_logout_redirect()`, `elementor_subscription_status_shortcode()`, `enqueue_talent_form_assets()`, `get_user_draft_profile()`, `handle_dashboard_image_upload()`, `handle_profile_image_ajax()`, `handle_talent_submission()`, `handle_talent_update()`, `hello_elementor_child_scripts_styles()`, `redirect_lost_password_page()`, `register_talent_meta_fields()`, `restrict_classified_ad_posting_plan_access()`, `restrict_talent_submission_if_profile_exists()`, `rifat_elementor_profile_picture_menu()`, `talent_add_meta_boxes()`, `talent_admin_scripts()`, `talent_details_meta_box_callback()`, `talent_save_meta_box_data()`, `talent_submission_success_message_shortcode()`, `ticketmaster_events_shortcode()`, `user_dashboard_shortcode()`, `user_has_talent_profile()`, `velvetreel_signup_form_shortcode()`, `velvetreel_track_profile_views()`, `wp_ajax_save_talent_progress()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 7`** (24 nodes): `talent-edit-controller.php`, `Talent_Edit_Controller`, `.check_access_permissions()`, `.__construct()`, `.get()`, `.get_ajax_url()`, `.get_array_meta()`, `.get_instance()`, `.get_languages_string()`, `.get_nonce()`, `.get_portfolio_cover_choice()`, `.get_profile_photo()`, `.get_state()`, `.get_talent_id()`, `.get_theme_version()`, `.has_profile_photo()`, `.init()`, `.load_required_files()`, `.load_talent_data()`, `.needs_physical_details()`, `.roles_needing_physical_details()`, `.setup_template_data()`, `.show_role_category()`, `.reset()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 9`** (17 nodes): `role-manager.js`, `RoleManager`, `.addPortfolioGallery()`, `.clearSelection()`, `.constructor()`, `.createDynamicFields()`, `.filterByDomain()`, `.getRoleData()`, `.getSelectedRole()`, `.init()`, `.loadRoleData()`, `.searchRoles()`, `.selectRole()`, `.setupEventListeners()`, `.setupFileUpload()`, `.showRoleSpecificFields()`, `.updateTitle()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 12`** (13 nodes): `POP3`, `.authorise()`, `.catchWarning()`, `.checkResponse()`, `.connect()`, `.disconnect()`, `.getErrors()`, `.getResponse()`, `.login()`, `.popBeforeSmtp()`, `.sendString()`, `.setError()`, `POP3.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 14`** (10 nodes): `add_advertisement_interests_menu()`, `display_advertisement_interests_compat_meta_box()`, `display_advertisement_interests_page()`, `display_advertisement_meta_box()`, `get_advertisements_with_interests_count()`, `advertisement-meta-boxes.php`, `register_advertisement_interests_compat_meta_box()`, `register_advertisement_meta_box()`, `save_advertisement_interest_date()`, `save_advertisement_meta_box()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 16`** (9 nodes): `DSNConfigurator`, `.applyConfig()`, `.configure()`, `.configureOptions()`, `.configureSMTP()`, `.mailer()`, `.parseDSN()`, `.parseUrl()`, `DSNConfigurator.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 18`** (6 nodes): `OAuth`, `.__construct()`, `.getGrant()`, `.getOauth64()`, `.getToken()`, `OAuth.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 19`** (5 nodes): `talent-form-app.js`, `TalentFormState`, `.constructor()`, `.get()`, `.initialize()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 24`** (4 nodes): `include_portfolio_gallery_component()`, `portfolio-gallery-functions.php`, `render_generic_role_fields()`, `role-fields-template.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 25`** (4 nodes): `ComposerAutoloaderInit2185d2f99bcd56787481d9357a5972d3`, `.getLoader()`, `.loadClassLoader()`, `autoload_real.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 31`** (3 nodes): `Exception`, `.errorMessage()`, `Exception.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 32`** (3 nodes): `ComposerStaticInit2185d2f99bcd56787481d9357a5972d3`, `.getInitializer()`, `autoload_static.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `SMTP` connect `Community 4` to `Community 8`, `Community 7`?**
  _High betweenness centrality (0.086) - this node is a cross-community bridge._
- **Should `Community 0` be split into smaller, more focused modules?**
  _Cohesion score 0.03 - nodes in this community are weakly interconnected._
- **Should `Community 1` be split into smaller, more focused modules?**
  _Cohesion score 0.06 - nodes in this community are weakly interconnected._
- **Should `Community 2` be split into smaller, more focused modules?**
  _Cohesion score 0.05 - nodes in this community are weakly interconnected._
- **Should `Community 3` be split into smaller, more focused modules?**
  _Cohesion score 0.05 - nodes in this community are weakly interconnected._
- **Should `Community 4` be split into smaller, more focused modules?**
  _Cohesion score 0.1 - nodes in this community are weakly interconnected._
- **Should `Community 5` be split into smaller, more focused modules?**
  _Cohesion score 0.08 - nodes in this community are weakly interconnected._