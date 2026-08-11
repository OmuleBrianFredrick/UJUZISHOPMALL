@component('mail::message')
# Welcome to UjuziShopMall, {{ $user->name }}!

Thank you for creating an account with us. We are thrilled to have you on board! Explore our wide range of products and discover the best deals tailored just for you.

@component('mail::button', ['url' => config('app.url')])
Start Shopping Now
@endcomponent

If you have any questions or need assistance, feel free to reply to this email. Our support team is always here to help.

Best regards,<br>
The {{ config('app.name') }} Team
@endcomponent
