<template>

    <div>

        <div v-if="loading" class="card loading">
            <span class="icon icon-circular-graph animation-spin"></span>
            {{ __('seo-pro::messages.report_is_being_generated')}}
        </div>

        <div v-else-if="!loading && report">
            <div class="flex flex-wrap -mx-4">
                <div class="w-1/3 px-4">
                    <div class="card py-2">
                        <h2 class="text-sm text-gray-700">{{ __('seo-pro::messages.generated') }}</h2>
                        <div class="text-lg"><relative-date :date="report.date"></relative-date></div>
                    </div>
                </div>
                <div class="w-1/3 px-4">
                    <div class="card py-2">
                        <h2 class="text-sm text-gray-700">{{ __('Pages Crawled') }}</h2>
                        <div class="text-lg">{{ report.pages.length }}</div>
                    </div>
                </div>
                <div class="w-1/3 px-4">
                    <div class="card py-2">
                        <h2 class="text-sm text-gray-700">{{ __('Site Score') }}</h2>
                        <div class="text-lg flex items-center">
                            <div class="bg-gray-200 h-3 w-full rounded flex mr-2 ">
                                <div class="h-3 rounded-l" :style="`width: ${report.score}%`" :class="{ 'bg-red-500': report.score < 70, 'bg-yellow-dark': report.score < 90, 'bg-green-500': report.score >= 90 }" />
                            </div>
                            <span>{{ report.score }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card p-0 mt-6">
                <table class="data-table">
                    <tbody>
                        <tr v-for="item in report.results">
                            <td class="w-8 text-center">
                                <status-icon :status="item.status"></status-icon>
                            </td>
                            <td class="pl-0">{{ item.description }}</td>
                            <td class="text-grey text-right">{{ item.comment }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="card p-0 mt-6">
                <table class="data-table">
                    <tbody>
                        <tr v-for="item in report.pages">
                            <td class="w-8 text-center">
                                <status-icon :status="item.status"></status-icon>
                            </td>
                            <td class="pl-0">
                                <a href="" @click.prevent="selected = item.id">{{ item.url }}</a>
                                <report-details
                                    v-if="selected === item.id"
                                    :item="item"
                                    @closed="selected = null"
                                ></report-details>
                            </td>
                            <td class="text-right text-xs pr-0 whitespace-no-wrap">
                                <a @click.prevent="selected = item.id" class="text-gray-700 mr-4 hover:text-grey-80" v-text="__('Details')"></a>
                                <a v-if="item.edit_url" target="_blank" :href="item.edit_url" class="mr-4 text-gray-700 hover:text-gray-800" v-text="__('Edit')"></a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>

    </div>

</template>

<script>
import ReportDetails from './Details.vue';
import RelativeDate from './RelativeDate.vue';
import StatusIcon from './StatusIcon.vue';

export default {

    components: {
        ReportDetails,
        RelativeDate,
        StatusIcon,
    },

    props: ['id'],

    data() {
        return {
            loading: false,
            report: null,
            selected: null
        }
    },

    mounted() {
        this.load();
    },

    methods: {

        load() {
            this.loading = true;
            this.report = null;

            Statamic.$request.get(cp_url(`seo-pro/reports/${this.id}`)).then(response => {
                if (response.data.status === 'pending') {
                    setTimeout(() => this.load(), 1000);
                    return;
                }

                this.report = response.data;
                this.loading = false;
            });
        }

    }

}
</script>
