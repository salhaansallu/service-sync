<template>
    <div class="container-fluid add-form-list">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Send SMS</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="sendSMS" action="" data-toggle="validator" onsubmit="return false;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Campaign Type <span class="text-danger">*</span></label>
                                        <select @change="changeType" ref="camp_type" class="form-control">
                                            <option value="quick">Quick Send</option>
                                            <option value="schedule">Schedule</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6" v-if="c_type == 'schedule'">
                                    <div class="form-group">
                                        <label>Send at <span class="text-danger">*</span></label>
                                        <VueDatePicker v-model="selectedDate" minutes-increment="5"></VueDatePicker>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Send to <span class="text-danger">*</span></label>
                                        <select @change="changeCusType" ref="send_to" class="form-control">
                                            <option value="all">Send to all customers</option>
                                            <option value="induvidual">Send to induvidual customer</option>
                                        </select>
                                    </div>
                                </div>

                                <div :class="'col-md-6 '">
                                    <div class="form-group">
                                        <label>Select Contact(s) <span class="text-danger">*</span></label>
                                        <select :disabled="cus_type" ref="contacts" class="form-control select2-multiple w-100" multiple="multiple">
                                            <option v-for="customer in customers" :value="customer.phone">{{ customer.name }} ({{ customer.phone }})</option>
                                        </select>
                                    </div>
                                </div>

                                <div :class="'col-md-6'" v-if="!cus_type">
                                    <div class="form-group">
                                        <label>Recipient Number(s) <span class="text-danger">*</span></label>
                                        <vue3-tags-input :tags="tags" @on-tags-changed="handleChangeTag"
                                            class="form-control" placeholder="Type Phone Numbers One By One" />
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Campaign Content <span class="text-danger">*</span></label>
                                        <textarea type="text" class="form-control" placeholder="Enter Message"
                                            ref="camp_message" value="" data-errors="Please Enter Campaign Content."
                                            rows="5" required></textarea>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                            </div>
                            <button :disabled="isDisabled" @click="sendSMS" type="submit" class="btn btn-primary mr-2">Send</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref } from 'vue';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import Vue3TagsInput from 'vue3-tags-input';

export default {
    components: { VueDatePicker, Vue3TagsInput },
    props: ['customers'],
    data() {
        return {
            selectedDate: new Date(new Date().setHours(0, 0, 0, 0)),
            tags: [],
            c_type: '',
            cus_type: true,
            isDisabled: false,
        };
    },
    methods: {
        handleChangeTag(tags) {
            this.tags = tags;
        },

        changeType() {
            this.c_type = this.$refs.camp_type.value
        },

        changeCusType() {
            if (this.$refs.send_to.value == 'induvidual') {
                this.cus_type = false;
            }
            else {
                this.cus_type = true;
            }
        },
        async sendSMS() {
            var camp_type = this.$refs.camp_type;
            var send_to = this.$refs.send_to;
            var camp_message = this.$refs.camp_message;
            var send_at = '';
            var contacts = '';

            if (this.c_type == "schedule") {
                if (this.selectedDate.getTime() < new Date().getTime()) {
                    toastr.error('Invalid date', 'Date Error');
                    return;
                }

                send_at = this.selectedDate.toISOString();
            }

            contacts = Array.from(this.$refs.contacts.selectedOptions).map(option => option.value);
            this.tags.forEach(element => {
                contacts.push(element);
            });
            
            if (send_to.value == "induvidual" && contacts.length <= 0) {
                toastr.error('Please select atleast 1 contact or enter 1 number', 'Error');
                return;
            }

            this.isDisabled= true;

            const { data } = await axios.post('/dashboard/sms/send', {
                camp_type: camp_type.value,
                send_to: send_to.value,
                send_at: send_at,
                contacts: JSON.stringify(contacts),
                camp_message: camp_message.value,
            }).catch((error) => {
                this.isDisabled= false;
            });

            if (data.error == 0) {
                
                toastr.success(data.msg, "Success");
                setTimeout(() => {
                    location.reload();
                }, 2000);
            }
            else {
                toastr.error(data.msg, "Error");
            }
        },
    },
};
</script>