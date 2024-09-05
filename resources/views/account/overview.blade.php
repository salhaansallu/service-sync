@extends('../account')

@section('account_content')
    <style>
        .addvert,
        footer {
            display: none;
        }
    </style>

    <script>
        @if (Session::has('error'))
            toastr.error("{{ Session::get('error') }}", 'Error');
        @endif
    </script>

    <div class="overview">
        <div class="subscribelist">
            <div class="head">Your subscribed services <i class="fa-solid fa-bars d-lg-none" id="account_menu"></i></div>
            <div class="sublist">
                @if ($has_pos)
                    @foreach ($pos as $system)
                        @if (!empty(company($system->pos_code)->pos_code))
                        <div class="service">
                            <div class="icon">
                                <i class="fa-solid fa-cash-register"></i>
                            </div>
                            <div class="name">{{ company($system->pos_code)->company_name }} <br>Cloud CRM</div>
                            <a href="/cloud-pos/{{ !empty($system->pos_code) ? CodeTourl(HashCode($system->pos_code)) : '' }}" class="primary-btn">Access</a>
                        </div>
                        @endif
                    @endforeach
                @endif

                @if ($has_dashboard)
                    <div class="service">
                        <div class="icon">
                            <i class="fa-solid fa-chart-line"></i>
                        </div>
                        <div class="name">Cloud Admin Dashboard</div>
                        <a href="/pos-dashboard/{{ !empty($admin_code) ? CodeTourl($admin_code) : '' }}"
                            class="primary-btn">Access</a>
                    </div>
                @endif
            </div>
        </div>

        <div class="subscribelist">
            <div class="head">Notifications</div>

            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Subject</th>
                        <th scope="col">Date</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($has_message)
                        <tr>
                            <td class="text-secondary">Cloud POS Invitation</td>
                            <td class="text-secondary">{{ date('d M Y', strtotime($invitation['created_at'])) }}</td>
                            <td><a href="/invitation/accept/{{ $invitation['invitation_id'] }}"><i
                                        class="fa-solid fa-check"></i> Accept</a></td>
                        </tr>
                    @endif

                    @if ($has_dashboard && $has_expired)
                        <tr>
                            <td class="text-danger">Your cloud POS subscription has been expired. Please renew it</td>
                            <td class="text-danger">{{ date('d M Y', strtotime($expiry['expiry_date'])) }}</td>
                            <td class="text-danger"><a href="/contact">Contact Us</a></td>
                        </tr>
                    @endif


                    @if (!$has_message && !$has_expired)
                    <tr><td colspan="3" class="text-secondary text-center">No notifications</td></tr>
                    @endif

                    {{-- <tr data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false"
                        aria-controls="collapseExample">
                        <td><i class="fa-solid fa-angle-right"></i> Subject of this message</td>
                        <td>12-11-2023</td>
                        <td><i class="fa-solid fa-check-double"></i> Mark as read</td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <div class="collapse" id="collapseExample">
                                <div class="card card-body border-0 text-secondary pt-0 ">
                                    Some placeholder content for the collapse component. This panel is hidden by default but
                                    revealed when the user activates the relevant trigger.
                                </div>
                            </div>
                        </td>
                    </tr> --}}

                </tbody>
            </table>
        </div>
    </div>
@endsection
