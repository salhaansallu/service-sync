<template>
    <div class="create_account cstep-1">
        <div class="container">
            <div class="heading">
                <h3 class="text-start"><span>NMSware Register</span></h3>
            </div>

            <div class="progresses">
                <div class="progress_head">
                    General info
                    <div class="progress_dot"></div>
                </div>
                <div class="progress_head">
                    Contact info
                    <div class="progress_dot"></div>
                </div>
                <div class="progress_head">
                    Complete
                    <div class="progress_dot"></div>
                </div>
            </div>
            <div class="progress_bar">
                <div class="inner_bar"></div>
            </div>

            <hr>

            <form action="" id="step-1" class="" onsubmit="return false;" autocomplete="off">
                <div class="row row-cols-1 row-cols-xxl-2 row-cols-xl-2 row-cols-lg-2 row-cols-md-2">
                    <div class="col">
                        <div class="input">
                            <div class="label">First Name</div>
                            <input type="text" placeholder="First Name" id="fname">
                        </div>
                    </div>
                    <div class="col">
                        <div class="input">
                            <div class="label">Last Name</div>
                            <input type="text" placeholder="Last Name" id="lname">
                        </div>
                    </div>

                    <div class="col">
                        <div class="input">
                            <div class="label">Company Name</div>
                            <input type="text" placeholder="Company Name" id="cname">
                        </div>
                    </div>
                    <div class="col">
                        <div class="input">
                            <div class="label">Industry</div>
                            <input type="text" placeholder="Industry" id="industry">
                        </div>
                    </div>

                    <div class="col">
                        <div class="input">
                            <div class="label">Company's Country</div>
                            <select name="" id="ccountry">
                                <option value="Sri Lanka">Sri Lanka</option>
                                <option :value="country" v-for="country in countries">{{ country }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col">
                        <div class="input">
                            <div class="label">Company's City</div>
                            <input type="text" placeholder="City" id="ccity">
                        </div>
                    </div>

                    <div class="col agreement">
                        By clicking "NEXT", you confirm that you have read and
                        understood the <span>NMSware Technologies</span> <a href="/privacy-policy" target="_blank">Privacy Policy</a>
                        and agree to it's terms.
                    </div>

                    <div class="col w-100">
                        <button class="primary-btn" @click="changeStep(2)">Next</button>
                    </div>
                </div>
            </form>

            <form action="" id="step-2" class="d-none" onsubmit="return false;" autocomplete="off">
                <div class="row row-cols-1 row-cols-xxl-2 row-cols-xl-2 row-cols-lg-2 row-cols-md-2">
                    <div class="col">
                        <div class="input">
                            <div class="label">Country</div>
                            <select name="" id="ccountry">
                                <option value="Sri Lanka">Sri Lanka</option>
                                <option :value="country" v-for="country in countries">{{ country }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col">
                        <div class="input">
                            <div class="label">Address</div>
                            <input type="text" placeholder="Address" id="address">
                        </div>
                    </div>

                    <div class="col">
                        <div class="input">
                            <div class="label">City</div>
                            <input type="text" placeholder="City" id="city">
                        </div>
                    </div>
                    <div class="col">
                        <div class="input">
                            <div class="label">Postal/Zip code</div>
                            <input type="text" placeholder="Postal/Zip code" id="zip">
                        </div>
                    </div>

                    <div class="col">
                        <div class="input">
                            <div class="label">Phone Number</div>
                            <input type="text" placeholder="Phone Number" id="phone">
                        </div>
                    </div>
                    <div class="col">
                    </div>

                    <div class="col agreement">
                        By clicking "NEXT", you confirm that you have read and
                        understood the <span>NMSware Technologies</span> <a href="/privacy" target="_blank">Privacy Policy</a>
                        and agree to it's terms.
                    </div>

                    <div class="col w-100">
                        <button class="primary-btn border-only" style="margin-right: 20px;"
                            @click="changeStep(1)">Back</button>
                        <button class="primary-btn" @click="changeStep(3)">Next</button>
                    </div>
                </div>
            </form>

            <div id="step-3" class="complete d-none">
                <div class="success">
                    <img :src="app_url+'/assets/images/auth/success.gif'" alt="">
                    <h4>Your details have been submitted</h4>
                    <p>Please wait...</p>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="loadingModal" tabindex="-1" role="dialog" aria-labelledby="loadingModalTitle"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-body d-flex" style="justify-content: center;">
                    <div class="spinner-border text-light" style="width: 4rem; height: 4rem;" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import toastr from 'toastr';
import { validateName, checkEmpty, validateCountry, validatePhone, getUrlParam } from '../custom';
import axios from 'axios';

export default {
    props: ['app_url'],
    data() {
        return {
            name: 'register_info',
            countries: [],
            registerData: [],
        }
    },
    methods: {
        async changeStep(step) {
            var fname = $('#fname');
            var lname = $('#lname');
            var cname = $('#cname');
            var industry = $('#industry');
            var ccountry = $('#ccountry');
            var ccity = $('#ccity');
            var country = $('#country');
            var address = $('#address');
            var city = $('#city');
            var zip = $('#zip');
            var phone = $('#phone');

            var allow = true;

            if (step == 2) {
                if (!validateName(fname.val())) {
                    toastr.error("Invalid First Name", "Error");
                    fname.addClass("form-control is-invalid");
                    allow = false;
                }
                else {
                    fname.removeClass("form-control is-invalid");
                    this.registerData['fname'] = fname.val();
                }
                if (!validateName(lname.val())) {
                    toastr.error("Invalid Last Name", "Error");
                    lname.addClass("form-control is-invalid");
                    allow = false;
                }
                else {
                    lname.removeClass("form-control is-invalid");
                    this.registerData['lname'] = lname.val();
                }
                if (checkEmpty(cname.val())) {
                    toastr.error("Invalid Company Name", "Error");
                    cname.addClass("form-control is-invalid");
                    allow = false;
                }
                else {
                    cname.removeClass("form-control is-invalid");
                    this.registerData['cname'] = cname.val();
                }
                if (checkEmpty(industry.val())) {
                    toastr.error("Invalid Industry", "Error");
                    industry.addClass("form-control is-invalid");
                    allow = false;
                }
                else {
                    industry.removeClass("form-control is-invalid");
                    this.registerData['industry'] = industry.val();
                }
                if (!validateCountry(ccountry.val())) {
                    toastr.error("Invalid Country", "Error");
                    ccountry.addClass("form-control is-invalid");
                    allow = false;
                }
                else {
                    ccountry.removeClass("form-control is-invalid");
                    this.registerData['ccountry'] = ccountry.val();
                }
                if (checkEmpty(ccity.val())) {
                    toastr.error("Invalid City", "Error");
                    ccity.addClass("form-control is-invalid");
                    allow = false;
                }
                else {
                    ccity.removeClass("form-control is-invalid");
                    this.registerData['ccity'] = ccity.val();
                }

                if (allow == true) {
                    for (let i = 1; i < 4; i++) {
                        $(".create_account").removeClass('cstep-' + i);
                        $("#step-" + i).addClass('d-none');
                    }
                    $(".create_account").addClass('cstep-' + 2);
                    $("#step-" + 2).removeClass('d-none');
                }
            }

            if (step == 3) {
                if (checkEmpty(address.val())) {
                    toastr.error("Invalid Address", "Error");
                    address.addClass("form-control is-invalid");
                    allow = false;
                }
                else {
                    address.removeClass("form-control is-invalid");
                    this.registerData['address'] = address.val();
                }
                if (checkEmpty(city.val())) {
                    toastr.error("Invalid City", "Error");
                    city.addClass("form-control is-invalid");
                    allow = false;
                }
                else {
                    city.removeClass("form-control is-invalid");
                    this.registerData['city'] = city.val();
                }
                if (checkEmpty(zip.val())) {
                    toastr.error("Invalid Zip code", "Error");
                    zip.addClass("form-control is-invalid");
                    allow = false;
                }
                else {
                    zip.removeClass("form-control is-invalid");
                    this.registerData['zip'] = zip.val();
                }
                if (validateCountry(country.val())) {
                    toastr.error("Invalid Country", "Error");
                    country.addClass("form-control is-invalid");
                    allow = false;
                }
                else {
                    ccountry.removeClass("form-control is-invalid");
                    this.registerData['country'] = ccountry.val();
                }
                if (!validatePhone(phone.val())) {
                    toastr.error("Invalid Phone Number", "Error");
                    phone.addClass("form-control is-invalid");
                    allow = false;
                }
                else {
                    phone.removeClass("form-control is-invalid");
                    this.registerData['phone'] = phone.val();
                }

                if (allow == true) {

                    $("#loadingModal").modal('show');

                    const data = await axios.post('/create-account', {
                        params: {
                            fname: this.registerData['fname'],
                            lname: this.registerData['lname'],
                            cname: this.registerData['cname'],
                            industry: this.registerData['industry'],
                            ccountry: this.registerData['ccountry'],
                            ccity: this.registerData['ccity'],
                            country: this.registerData['country'],
                            address: this.registerData['address'],
                            city: this.registerData['city'],
                            zip: this.registerData['zip'],
                            phone: this.registerData['phone'],
                        }
                    });

                    $("#loadingModal").modal('hide');
                    setTimeout(() => {
                        $("#loadingModal").modal('hide');
                    }, 1000);

                    if (data.data['error'] == 0) {
                        for (let i = 1; i < 4; i++) {
                            $(".create_account").removeClass('cstep-' + i);
                            $("#step-" + i).addClass('d-none');
                        }
                        $(".create_account").addClass('cstep-' + 3);
                        $("#step-" + 3).removeClass('d-none');

                        setTimeout(function () {
                            location.href="/account/overview";
                        }, 3000);
                    }
                }
            }
        },
    },
    mounted() {
        this.countries = validateCountry('get');
    }
}
</script>
