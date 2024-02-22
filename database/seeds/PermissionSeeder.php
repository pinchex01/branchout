<?php

use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('permissions')->delete();
        \DB::insert("insert into permissions (display_name,name,type) VALUES 
            ('Change Settings','change-settings' ,'system'),
            ('Change Organiser Settings','change-merchant-settings' ,'merchant'),
            ('View Dashboard','view-dashboard','all'),
            ('View Roles','view-roles','system'),
            ('Create Roles','create-roles','system'),
            ('Update Roles','update-roles','system'),
            ('Create Users','create-users','system'),
            ('View Users','view-users','system'),
            ('Resend User Activation','resend-user-activation','all'),
            ('Update Users','update-users','system'),
            ('Deactivate Users','delete-users','system'),
            ('Create Events','create-events','all'),
            ('View Events','view-events','all'),
            ('Update Events','update-events','all'),
            ('Disable Events','delete-events','all'),
            ('Create Tickets','create-tickets','all'),
            ('View Tickets','view-tickets','all'),
            ('Update Tickets','update-tickets','all'),
            ('Disable Tickets','delete-tickets','all'),
            ('View Orders','view-orders','all'),
            ('Update Orders','update-orders','all'),
            ('Terminate Orders','delete-orders','all'),
            ('Invite Attendee','create-attendees','all'),
            ('View Attendee','view-attendees','all'),
            ('Update Attendee','update-attendees','all'),
            ('Remove Attendee','delete-attendees','all'),
            ('Create Invoice','create-invoices','all'),
            ('View Invoice','view-invoices','all'),
            ('View Banks','view-banks','all'),
            ('Add Bank','create-banks','system'),
            ('Update Bank','update-banks','system'),
             ('Add Bank Account','create-banks-accounts','all'),
            ('Update Bank Accounts','update-banks-accounts','all'),
            ('View Payments','view-payments','all'),
            ('Verify Payments','verify-payments','all'),
            ('View Settlements','view-settlements','all'),
            ('Make Withdrawal','create-settlements','all'),
            ('View Staff','view-staffs','all'),
            ('Add Staff','create-staffs','all'),
            ('Update Staff','update-staffs','all'),
            ('Remove Staff','delete-staffs','all'),
            ('View Organisers','view-merchants','system'),
            ('Add Organisers','create-merchants','system'),
            ('Update Organisers','update-merchants','all'),
            ('Disable Organisers','delete-merchants','all'),
            ('View Templates','view-templates','system'),
            ('Create Templates','create-templates','system'),
            ('Update Templates','update-templates','system'),
            ('Disable Templates','delete-templates','system'),
            ('View Statements','view-statements','all'),
            ('Create Payments','create-payments','all'),
            ('View Uploads','view-uploads','system'),
            ('Delete Uploads','delete-uploads','system'),
            ('View Reports','view-reports','all'),
            ('View Tariffs','view-tariffs' ,'system'),
            ('Add Tariff','create-tariffs' ,'system'),
            ('Edit Tariff','edit-tariffs' ,'system'),
            ('View All Applications','view-applications','system'),
            ('Review Applications','review-applications','system'),
            ('Approve Applications','approve-applications','system')
      ");
    }
}
