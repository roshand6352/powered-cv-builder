@extends('web.layouts.marketing')

@section('content')
    @include('web.landing.partials.page-hero', [
        'title' => 'Contact Us',
        'description' => 'Have a question or need help? We typically respond within one business day.',
    ])

    <section class="page-section page-section--white">
        <div class="container">
            <div class="contact-grid">
                <div class="contact-info">
                    <h2>Get in touch</h2>
                    <p>Whether you need help with your CV, have billing questions, or want to discuss enterprise plans — our team is here to help.</p>

                    <dl class="contact-dl">
                        <div>
                            <dt>Email</dt>
                            <dd><a href="mailto:support@example.com" class="link-orange">support@example.com</a></dd>
                        </div>
                        <div>
                            <dt>Support hours</dt>
                            <dd>Monday – Friday, 9am – 6pm SAST</dd>
                        </div>
                        <div>
                            <dt>Enterprise sales</dt>
                            <dd><a href="mailto:sales@example.com" class="link-orange">sales@example.com</a></dd>
                        </div>
                    </dl>
                </div>

                <div class="contact-card">
                    <form action="#" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" name="name" id="name" required class="form-input">
                        </div>
                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" required class="form-input">
                        </div>
                        <div class="form-group">
                            <label for="subject" class="form-label">Subject</label>
                            <select name="subject" id="subject" class="form-select">
                                <option>General inquiry</option>
                                <option>Billing & subscriptions</option>
                                <option>Technical support</option>
                                <option>Enterprise / Team plan</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="message" class="form-label">Message</label>
                            <textarea name="message" id="message" rows="5" required class="form-textarea"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Send Message</button>
                        <p class="form-note">Form submission will be wired to backend in a future update.</p>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
