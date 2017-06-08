SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `ad_fees` (
  `adfee_id` int(11) NOT NULL,
  `adfee_show_val_min` float NOT NULL,
  `adfee_show_val_max` float NOT NULL,
  `adfee_val` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `app_impressions` (
  `app_impression_id` int(11) NOT NULL,
  `app_impression_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `app_impression_show_val_min` float NOT NULL,
  `app_impression_show_val_max` float NOT NULL,
  `app_impression_calc_val_min` float NOT NULL,
  `app_impression_calc_val_max` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `category_max` float NOT NULL,
  `category_min` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `clients` (
  `client_id` int(11) NOT NULL,
  `client_code` int(11) DEFAULT NULL,
  `profile_guid` varchar(33) COLLATE utf8_unicode_ci DEFAULT NULL,
  `profile_guid_last_viewed_when` datetime DEFAULT NULL,
  `is_lead` tinyint(4) NOT NULL DEFAULT '0',
  `client_name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `client_sector_id` int(11) DEFAULT NULL,
  `client_sector_sub_id` int(11) DEFAULT NULL,
  `client_source_id` int(11) DEFAULT NULL,
  `client_rating_id` int(11) DEFAULT NULL,
  `profile_sent` tinyint(4) DEFAULT NULL,
  `profile_sent_when` date DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `manager_name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `manager_name2` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `area` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `telephone` varchar(15) CHARACTER SET latin1 DEFAULT NULL,
  `mobile` varchar(15) CHARACTER SET latin1 DEFAULT NULL,
  `email` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email2` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `facebook_page` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `website` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `comment` text COLLATE utf8_unicode_ci,
  `owned_date` datetime DEFAULT NULL,
  `owner` int(100) DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL,
  `modified_by` int(100) DEFAULT NULL,
  `has_facebook_page_before` tinyint(4) DEFAULT NULL,
  `next_renewal` datetime DEFAULT NULL,
  `room_exists` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `clients_pages` (
  `client_page_id` int(11) NOT NULL,
  `client_id` int(11) DEFAULT NULL,
  `client_page` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `client_appointments` (
  `client_appointment_id` int(11) NOT NULL,
  `client_appointment_client_id` int(11) DEFAULT NULL,
  `client_appointment_is_lead` tinyint(4) DEFAULT NULL,
  `client_appointment_datetime` datetime DEFAULT NULL,
  `client_appointment_location` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `client_appointment_google` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `client_appointment_comment` text COLLATE utf8_unicode_ci,
  `client_appointment_owner_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `client_appointment_participants` (
  `client_appointment_participant_id` int(11) NOT NULL,
  `client_appointment_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `client_appointment_participant_is_coordinator` tinyint(4) DEFAULT NULL,
  `client_appointment_participant_comment` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `client_calls` (
  `client_call_id` int(11) NOT NULL,
  `client_id` int(11) DEFAULT NULL,
  `client_call_datetime` datetime DEFAULT NULL,
  `client_call_discussion` text COLLATE utf8_unicode_ci,
  `client_call_next_call` datetime DEFAULT NULL,
  `chk_answered` tinyint(4) DEFAULT NULL,
  `chk_company_presented` tinyint(4) DEFAULT NULL,
  `chk_company_profile` tinyint(4) DEFAULT NULL,
  `chk_client_proposal` tinyint(4) DEFAULT NULL,
  `chk_appointment_booked` tinyint(4) DEFAULT NULL,
  `comment` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `client_invoice_details` (
  `client_invoice_detail_id` int(11) NOT NULL,
  `client_id` int(11) DEFAULT NULL,
  `company_name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `occupation` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pobox` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `vat_no` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tax_office_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `client_ratings` (
  `client_rating_id` int(11) NOT NULL,
  `client_rating_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `client_sectors` (
  `client_sector_id` int(11) NOT NULL,
  `client_sector_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `client_sector_subs` (
  `client_sector_sub_id` int(11) NOT NULL,
  `client_sector_sub_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `client_sector_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `client_sources` (
  `client_source_id` int(11) NOT NULL,
  `client_source_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `countries` (
  `country_id` int(11) NOT NULL,
  `country_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `country_min` float NOT NULL,
  `country_max` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `expenses` (
  `expense_id` int(11) NOT NULL,
  `expense_template_id` int(11) DEFAULT NULL,
  `cost` decimal(15,2) DEFAULT NULL,
  `pay_method` int(11) DEFAULT NULL,
  `daterec` date DEFAULT NULL,
  `misc_title` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `misc_daterec` date DEFAULT NULL,
  `misc_is_paid` tinyint(4) DEFAULT NULL,
  `comments` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `owner_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `expense_categories` (
  `expense_category_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `expense_category_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `expense_templates` (
  `expense_template_id` int(11) NOT NULL,
  `expense_category_id` int(11) DEFAULT NULL,
  `expense_sub_category_id` int(11) DEFAULT NULL,
  `price` decimal(15,2) DEFAULT NULL,
  `created_owner_id` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `fb_request_proposals` (
  `fb_request_proposal_id` int(11) NOT NULL,
  `company_name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `manager_name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `telephone` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `town` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `facebook` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ad_budget` float(15,2) DEFAULT NULL,
  `likes_switch` tinyint(4) DEFAULT NULL,
  `post_engag_switch` tinyint(4) DEFAULT NULL,
  `conv_eshop_switch` tinyint(4) DEFAULT NULL,
  `website_clicks_switch` tinyint(4) DEFAULT NULL,
  `app_switch` tinyint(4) DEFAULT NULL,
  `content_manage_switch` tinyint(4) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `comments` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `owned_date` date DEFAULT NULL,
  `daterec` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `hr_records` (
  `hr_record_id` int(11) NOT NULL,
  `full_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gender` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fb_id` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tel` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `portofolio` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `skills` text COLLATE utf8_unicode_ci,
  `dob` date DEFAULT NULL,
  `filename` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `daterec` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `impressions` (
  `impression_id` int(11) NOT NULL,
  `impression_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `impression_show_val_min` float NOT NULL,
  `impression_show_val_max` float NOT NULL,
  `impression_calc_val_min` float NOT NULL,
  `impression_calc_val_max` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `logger` (
  `log_id` int(11) NOT NULL,
  `log_UTC_when` datetime DEFAULT NULL,
  `log_type` tinyint(4) DEFAULT NULL,
  `log_text` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `user_notified` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `namedays` (
  `nameday_id` int(11) NOT NULL,
  `day` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `names` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `offers` (
  `offer_id` int(11) NOT NULL,
  `offer_no` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `rec_guid` varchar(33) COLLATE utf8_unicode_ci DEFAULT NULL,
  `rec_guid_answer` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `rec_guid_last_viewed_when` datetime DEFAULT NULL,
  `rec_guid_answer_invoice` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `rec_guid_invoice_last_viewed_when` datetime DEFAULT NULL,
  `offer_seller_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `offer_seller_id` int(11) DEFAULT NULL,
  `offer_date_rec` datetime NOT NULL,
  `offer_page_url` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `offer_company_name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `offer_company_manager_name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `offer_proposal_date` date DEFAULT NULL,
  `offer_proposal_valid_date` date DEFAULT NULL,
  `offer_posting_management` tinyint(4) DEFAULT NULL,
  `offer_apps` tinyint(4) DEFAULT NULL,
  `apps_name_change` tinyint(4) DEFAULT NULL,
  `apps_page_merge` tinyint(4) DEFAULT NULL,
  `offer_apps_page_merge` tinyint(4) DEFAULT NULL,
  `offer_apps_page_merge_url_one` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `offer_apps_page_merge_url_two` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `offer_apps_name_change` tinyint(4) DEFAULT NULL,
  `offer_apps_name_change_old_page_name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `offer_apps_name_change_old_page_url` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `offer_apps_name_change_new_page_name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `offer_apps_name_change_new_page_url` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `offer_city` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `offer_email` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `offer_telephone` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `offer_total_amount` float(15,2) DEFAULT NULL,
  `app_impression_min` int(11) DEFAULT NULL,
  `app_impression_max` int(11) DEFAULT NULL,
  `country_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `offer_contract_period` int(11) NOT NULL,
  `offer_adverts_platform` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `apps` int(11) DEFAULT NULL,
  `gen_page` tinyint(4) DEFAULT NULL,
  `gen_aprice` float DEFAULT NULL,
  `gen_aprice_app_cost` float DEFAULT NULL,
  `gen_aprice_pages_cost` float DEFAULT NULL,
  `gen_post_manage` float DEFAULT NULL,
  `gen_a_pprice` float DEFAULT NULL,
  `gen_pprice` float DEFAULT NULL,
  `gen_a_budget` float DEFAULT NULL,
  `gen_budget` float DEFAULT NULL,
  `gen_extra_budget` float DEFAULT NULL,
  `gen_a_fee` float DEFAULT NULL,
  `gen_fee` float DEFAULT NULL,
  `gen_fee_discount_percentage` int(11) DEFAULT NULL,
  `gen_fee_discount_money` float DEFAULT NULL,
  `gen_subtotal` float DEFAULT NULL,
  `gen_tax` float DEFAULT NULL,
  `gen_total` float DEFAULT NULL,
  `gen_olikes` int(11) DEFAULT NULL,
  `gen_otat` int(11) DEFAULT NULL,
  `gen_n1likes` int(11) DEFAULT NULL,
  `gen_n2likes` int(11) DEFAULT NULL,
  `gen_n1tat` int(11) DEFAULT NULL,
  `gen_n2tat` int(11) DEFAULT NULL,
  `gen_n1views` int(11) DEFAULT NULL,
  `gen_n2views` int(11) DEFAULT NULL,
  `gen_nviews` int(11) DEFAULT NULL,
  `gen_webclicks_min` int(11) DEFAULT NULL,
  `gen_webclicks_max` int(11) DEFAULT NULL,
  `comment` text COLLATE utf8_unicode_ci,
  `company_id` int(11) DEFAULT NULL,
  `next_renewal` date DEFAULT NULL,
  `service_starts` date DEFAULT NULL,
  `service_ends` date DEFAULT NULL,
  `marketing_plan_when` datetime DEFAULT NULL,
  `marketing_plan_location` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `marketing_plan_attachment` int(11) DEFAULT NULL,
  `marketing_plan_completed` tinyint(4) DEFAULT NULL,
  `request_access` tinyint(4) DEFAULT NULL,
  `is_paid` tinyint(4) DEFAULT NULL,
  `is_paid_when` date DEFAULT NULL,
  `fb_likes_start` int(11) DEFAULT NULL,
  `fb_likes_end` int(11) DEFAULT NULL,
  `fb_tat_start` int(11) DEFAULT NULL,
  `fb_tat_end` int(11) DEFAULT NULL,
  `fb_engag_start` int(11) DEFAULT NULL,
  `fb_engag_end` int(11) DEFAULT NULL,
  `offer_type` int(11) DEFAULT NULL,
  `invoice_detail_id` int(11) DEFAULT NULL,
  `invoice_detail_when` datetime DEFAULT NULL,
  `invoice_detail_user` int(11) DEFAULT NULL,
  `invoice_sent_when` datetime DEFAULT NULL,
  `invoice_sent_user` int(11) DEFAULT NULL,
  `offer_sent_by_mail` datetime DEFAULT NULL,
  `approval_company_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `approval_manager_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `approval_user_date` date DEFAULT NULL,
  `approval_daterec` datetime DEFAULT NULL,
  `service_start_likes` bigint(20) DEFAULT NULL,
  `service_start_tat` bigint(20) DEFAULT NULL,
  `service_end_likes` bigint(20) DEFAULT NULL,
  `service_end_tat` bigint(20) DEFAULT NULL,
  `app_ad_budget` float DEFAULT NULL,
  `language` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `send_ppl_website` int(11) DEFAULT NULL,
  `send_ppl_website_cost` float(15,2) DEFAULT NULL,
  `increase_conversions` int(11) DEFAULT NULL,
  `increase_conversions_cost` float(15,2) DEFAULT NULL,
  `boost_posts` int(11) DEFAULT NULL,
  `boost_posts_cost` float(15,2) DEFAULT NULL,
  `promote_page_likes` int(11) DEFAULT NULL,
  `promote_page_likes_cost` float(15,2) DEFAULT NULL,
  `get_installs_app` int(11) DEFAULT NULL,
  `get_installs_app_cost` float(15,2) DEFAULT NULL,
  `increase_engag` int(11) DEFAULT NULL,
  `increase_engag_cost` float(15,2) DEFAULT NULL,
  `raise_attendance` int(11) DEFAULT NULL,
  `raise_attendance_cost` float(15,2) DEFAULT NULL,
  `claim_offer` int(11) DEFAULT NULL,
  `claim_offer_cost` float(15,2) DEFAULT NULL,
  `video_views` int(11) DEFAULT NULL,
  `video_views_cost` float(15,2) DEFAULT NULL,
  `graphics_switch` tinyint(4) DEFAULT NULL,
  `marketing_plan_comment` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_deleted` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `offer_advertise_details` (
  `offer_advertise_detail_id` int(11) NOT NULL,
  `offer_id` int(11) DEFAULT NULL,
  `user4_send_ppl_website` int(11) DEFAULT NULL,
  `user4_increase_conversions` int(11) DEFAULT NULL,
  `user4_boost_posts` int(11) DEFAULT NULL,
  `user4_promote_page_likes` int(11) DEFAULT NULL,
  `user4_get_installs_app` int(11) DEFAULT NULL,
  `user4_increase_engag` int(11) DEFAULT NULL,
  `user4_raise_attendance` int(11) DEFAULT NULL,
  `user4_claim_offer` int(11) DEFAULT NULL,
  `user4_video_views` int(11) DEFAULT NULL,
  `ad_keywords` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ad_client_goals` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ad_fb1` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ad_fb2` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ad_fb3` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ad_fb4` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `aud_countries` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `aud_age_min` smallint(6) DEFAULT NULL,
  `aud_age_max` smallint(6) DEFAULT NULL,
  `aud_gender` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `aud_languages` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `aud_interests` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `aud_behaviors` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ad_connections` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ad_placement_mobile` smallint(6) DEFAULT NULL,
  `ad_placement_desktop` smallint(6) DEFAULT NULL,
  `ad_placement_desktop_right` smallint(6) DEFAULT NULL,
  `ad_placement_audience_network` smallint(6) DEFAULT NULL,
  `daterec` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `offer_page_details` (
  `offer_page_detail_id` int(11) NOT NULL,
  `offer_id` int(11) DEFAULT NULL,
  `is_creation` tinyint(4) DEFAULT NULL,
  `domain` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `reviews` tinyint(4) DEFAULT NULL,
  `call_action` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `website` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cover_photo_change` tinyint(4) DEFAULT NULL,
  `profile_photo_change` tinyint(4) DEFAULT NULL,
  `short_description` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `daterec` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `offer_room_details` (
  `offer_room_detail_id` int(11) NOT NULL,
  `offer_id` int(11) DEFAULT NULL,
  `create_room` tinyint(4) DEFAULT NULL,
  `room_type` tinyint(4) DEFAULT NULL,
  `room_name` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `account_manager` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `account_executive_id` int(11) DEFAULT NULL,
  `posts_per_week` smallint(6) DEFAULT NULL,
  `graphics` tinyint(4) DEFAULT NULL,
  `post_language` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `privacy` smallint(6) DEFAULT NULL,
  `email1` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email2` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email3` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email4` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
  `comment` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_deleted` tinyint(4) NOT NULL,
  `daterec` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `tat` (
  `tat_id` int(11) NOT NULL,
  `tat_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `show_val_min` float NOT NULL,
  `show_val_max` float NOT NULL,
  `calc_val_min` float NOT NULL,
  `calc_val_max` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `tax_offices` (
  `tax_office_id` int(11) NOT NULL,
  `tax_office_name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country_id` int(2) DEFAULT NULL,
  `tax_office_code` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tax_office_prefecture` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `transaction_methods` (
  `transaction_method_id` int(11) NOT NULL,
  `transaction_method_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_level_id` int(11) NOT NULL,
  `mail` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `prefix` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fullname` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `last_logon` datetime DEFAULT NULL,
  `signature` varchar(200) CHARACTER SET latin1 DEFAULT NULL,
  `reply_mail` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `user_levels` (
  `user_level_id` int(11) NOT NULL,
  `user_level_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `user_scores` (
  `user_score_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `score_when` date DEFAULT NULL,
  `score` float DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `user_vacations` (
  `user_vacation_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `date_start` date DEFAULT NULL,
  `date_end` date DEFAULT NULL,
  `authorized` tinyint(4) DEFAULT NULL,
  `comment` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `user_working_hours` (
  `user_working_hour_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `date_start` datetime DEFAULT NULL,
  `date_end` datetime DEFAULT NULL,
  `logout_type` int(11) DEFAULT NULL,
  `reason` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `web_clicks` (
  `web_click_id` int(11) NOT NULL,
  `web_click_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `web_click_show_val_min` float NOT NULL,
  `web_click_show_val_max` float NOT NULL,
  `web_click_calc_val_min` float NOT NULL,
  `web_click_calc_val_max` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


ALTER TABLE `ad_fees`
  ADD PRIMARY KEY (`adfee_id`);

ALTER TABLE `app_impressions`
  ADD PRIMARY KEY (`app_impression_id`);

ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

ALTER TABLE `clients`
  ADD PRIMARY KEY (`client_id`);

ALTER TABLE `clients_pages`
  ADD PRIMARY KEY (`client_page_id`);

ALTER TABLE `client_appointments`
  ADD PRIMARY KEY (`client_appointment_id`);

ALTER TABLE `client_appointment_participants`
  ADD PRIMARY KEY (`client_appointment_participant_id`);

ALTER TABLE `client_calls`
  ADD PRIMARY KEY (`client_call_id`);

ALTER TABLE `client_invoice_details`
  ADD PRIMARY KEY (`client_invoice_detail_id`);

ALTER TABLE `client_ratings`
  ADD PRIMARY KEY (`client_rating_id`);

ALTER TABLE `client_sectors`
  ADD PRIMARY KEY (`client_sector_id`);

ALTER TABLE `client_sector_subs`
  ADD PRIMARY KEY (`client_sector_sub_id`);

ALTER TABLE `client_sources`
  ADD PRIMARY KEY (`client_source_id`);

ALTER TABLE `countries`
  ADD PRIMARY KEY (`country_id`);

ALTER TABLE `expenses`
  ADD PRIMARY KEY (`expense_id`);

ALTER TABLE `expense_categories`
  ADD PRIMARY KEY (`expense_category_id`);

ALTER TABLE `expense_templates`
  ADD PRIMARY KEY (`expense_template_id`);

ALTER TABLE `fb_request_proposals`
  ADD PRIMARY KEY (`fb_request_proposal_id`);

ALTER TABLE `hr_records`
  ADD PRIMARY KEY (`hr_record_id`);

ALTER TABLE `impressions`
  ADD PRIMARY KEY (`impression_id`);

ALTER TABLE `logger`
  ADD PRIMARY KEY (`log_id`);

ALTER TABLE `namedays`
  ADD PRIMARY KEY (`nameday_id`);

ALTER TABLE `offers`
  ADD PRIMARY KEY (`offer_id`);

ALTER TABLE `offer_advertise_details`
  ADD PRIMARY KEY (`offer_advertise_detail_id`);

ALTER TABLE `offer_page_details`
  ADD PRIMARY KEY (`offer_page_detail_id`);

ALTER TABLE `offer_room_details`
  ADD PRIMARY KEY (`offer_room_detail_id`);

ALTER TABLE `tat`
  ADD PRIMARY KEY (`tat_id`);

ALTER TABLE `tax_offices`
  ADD PRIMARY KEY (`tax_office_id`);

ALTER TABLE `transaction_methods`
  ADD PRIMARY KEY (`transaction_method_id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

ALTER TABLE `user_levels`
  ADD PRIMARY KEY (`user_level_id`);

ALTER TABLE `user_scores`
  ADD PRIMARY KEY (`user_score_id`);

ALTER TABLE `user_vacations`
  ADD PRIMARY KEY (`user_vacation_id`);

ALTER TABLE `user_working_hours`
  ADD PRIMARY KEY (`user_working_hour_id`);

ALTER TABLE `web_clicks`
  ADD PRIMARY KEY (`web_click_id`);


ALTER TABLE `ad_fees`
  MODIFY `adfee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
ALTER TABLE `app_impressions`
  MODIFY `app_impression_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
ALTER TABLE `clients`
  MODIFY `client_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=642;
ALTER TABLE `clients_pages`
  MODIFY `client_page_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=671;
ALTER TABLE `client_appointments`
  MODIFY `client_appointment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;
ALTER TABLE `client_appointment_participants`
  MODIFY `client_appointment_participant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=127;
ALTER TABLE `client_calls`
  MODIFY `client_call_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1961;
ALTER TABLE `client_invoice_details`
  MODIFY `client_invoice_detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
ALTER TABLE `client_ratings`
  MODIFY `client_rating_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
ALTER TABLE `client_sectors`
  MODIFY `client_sector_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
ALTER TABLE `client_sector_subs`
  MODIFY `client_sector_sub_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;
ALTER TABLE `client_sources`
  MODIFY `client_source_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
ALTER TABLE `countries`
  MODIFY `country_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
ALTER TABLE `expenses`
  MODIFY `expense_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=158;
ALTER TABLE `expense_categories`
  MODIFY `expense_category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;
ALTER TABLE `expense_templates`
  MODIFY `expense_template_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;
ALTER TABLE `fb_request_proposals`
  MODIFY `fb_request_proposal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `hr_records`
  MODIFY `hr_record_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;
ALTER TABLE `impressions`
  MODIFY `impression_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
ALTER TABLE `logger`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
ALTER TABLE `namedays`
  MODIFY `nameday_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=442;
ALTER TABLE `offers`
  MODIFY `offer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=352;
ALTER TABLE `offer_advertise_details`
  MODIFY `offer_advertise_detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
ALTER TABLE `offer_page_details`
  MODIFY `offer_page_detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `offer_room_details`
  MODIFY `offer_room_detail_id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `tat`
  MODIFY `tat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
ALTER TABLE `tax_offices`
  MODIFY `tax_office_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=239;
ALTER TABLE `transaction_methods`
  MODIFY `transaction_method_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
ALTER TABLE `user_levels`
  MODIFY `user_level_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
ALTER TABLE `user_scores`
  MODIFY `user_score_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
ALTER TABLE `user_vacations`
  MODIFY `user_vacation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
ALTER TABLE `user_working_hours`
  MODIFY `user_working_hour_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=627;
ALTER TABLE `web_clicks`
  MODIFY `web_click_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
