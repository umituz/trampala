@extends('layouts.email.base')

@section('title', 'Welcome to ' . config('app.name'))

@section('content')
    {{-- Pass variables to components --}}
    @php
        $headerSubtitle = 'Welcome to Trampala - Your listing platform!';
        $showQuickLinks = true;
        $showUnsubscribe = true;
    @endphp

    <h1 style="color: #111827; font-size: 28px; margin-bottom: 20px; text-align: center;">
        🎉 Welcome to Trampala, {{ $user->name }}!
    </h1>
    
    <p style="color: #374151; font-size: 18px; line-height: 1.6; margin-bottom: 30px; text-align: center;">
        Thank you for joining Trampala! We're excited to have you as part of our listing community where you can create, share and discover amazing listings.
    </p>
    
    {{-- Welcome Benefits --}}
    @include('layouts.email.components.info-box', [
        'title' => '📋 What You Can Do on Trampala',
        'style' => 'success',
        'items' => [
            ['label' => '📝 Create Listings', 'value' => 'Post your items with photos and descriptions'],
            ['label' => '🔍 Browse Listings', 'value' => 'Discover amazing items from our community'],
            ['label' => '⚡ Quick Approval', 'value' => 'Our admin team reviews listings promptly'],
            ['label' => '🏷️ Categories', 'value' => 'Organize listings by category and location']
        ]
    ])
    
    {{-- Get Started Buttons --}}
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 30px 0;">
        <tr>
            <td align="center">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td style="padding: 0 10px;">
                            @include('layouts.email.components.button', [
                                'url' => config('app.frontend_url') . '/listings/create',
                                'text' => '📝 Create Your First Listing',
                                'color' => 'primary',
                                'align' => 'center'
                            ])
                        </td>
                        <td style="padding: 0 10px;">
                            @include('layouts.email.components.button', [
                                'url' => config('app.frontend_url') . '/listings',
                                'text' => '🔍 Browse Listings',
                                'color' => 'info',
                                'align' => 'center'
                            ])
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    
    {{-- Quick Tips --}}
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-top: 30px;">
        <tr>
            <td style="background-color: #eff6ff; border: 1px solid #dbeafe; border-radius: 8px; padding: 20px;">
                <h3 style="color: #1e40af; font-size: 16px; margin: 0 0 15px 0; text-align: center;">
                    💡 Quick Tips for New Trampala Users
                </h3>
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td style="color: #1e40af; font-size: 14px; line-height: 1.6;">
                            <p style="margin: 0 0 10px 0;">
                                📷 <strong>Add quality photos</strong> to your listings to attract more views
                            </p>
                            <p style="margin: 0 0 10px 0;">
                                📝 <strong>Write detailed descriptions</strong> to help buyers understand your items
                            </p>
                            <p style="margin: 0;">
                                ⚡ <strong>Your listings need admin approval</strong> before they go live on the platform
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    
    {{-- Contact Section --}}
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-top: 30px;">
        <tr>
            <td style="text-align: center; padding: 20px 0; border-top: 1px solid #e5e7eb;">
                <h3 style="color: #374151; font-size: 16px; margin: 0 0 10px 0;">
                    Need Help Creating Your First Listing?
                </h3>
                <p style="color: #6b7280; font-size: 14px; line-height: 1.6; margin: 0;">
                    Our support team is here to help you get started with Trampala.<br>
                    Have questions about the listing process or platform features? Contact us!
                </p>
            </td>
        </tr>
    </table>
    
    {{-- Thank You Message --}}
    <p style="color: #374151; font-size: 16px; line-height: 1.6; margin-top: 30px; margin-bottom: 10px; text-align: center;">
        <strong>Happy Listing!</strong>
    </p>
    
    <p style="color: #374151; font-size: 16px; line-height: 1.6; margin: 0; text-align: center;">
        <strong>Welcome to the Trampala community,</strong><br>
        The Trampala Team
    </p>
@endsection