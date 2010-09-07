-- **********************************************************
-- *                                                        *
-- * IMPORTANT NOTE                                         *
-- *                                                        *
-- * Do not import this file manually but use the TYPOlight *
-- * install tool to create and maintain database tables!   *
-- *                                                        *
-- **********************************************************

-- 
-- Table `tl_page`
-- 

CREATE TABLE `tl_page` (
  `auto_activate_registration` char(1) NOT NULL default '',
  `auto_login_registration` char(1) NOT NULL default '',
  `auto_login_activation` char(1) NOT NULL default '',
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

