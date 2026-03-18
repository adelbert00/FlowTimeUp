<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { ref, watch } from 'vue';
import CustomDatePicker from '@/Components/CustomDatePicker.vue';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/Components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/Components/ui/tabs';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { Checkbox } from '@/Components/ui/checkbox';
import { Button } from '@/Components/ui/button';
import { Download, FileText, BarChart3, Tag as TagIcon, Folder } from 'lucide-vue-next';

interface SummaryData {
  id: number | string;
  name: string;
  duration: number;
  earnings: number;
  color?: string;
}

interface Props {
  projects: any[];
  tags: any[];
  projectSummary: SummaryData[];
  tagSummary: SummaryData[];
  filters: {
    project_id: string | number | null;
    tag_id: string | number | null;
    start_date: string;
    end_date: string;
    include_billable: boolean | string;
    include_non_billable: boolean | string;
  };
}

const props = defineProps<Props>();

const filterState = ref({
  project_id: props.filters.project_id?.toString() || 'all',
  tag_id: props.filters.tag_id?.toString() || 'all',
  start_date: props.filters.start_date,
  end_date: props.filters.end_date,
  include_billable: String(props.filters.include_billable) === 'true',
  include_non_billable: String(props.filters.include_non_billable) === 'true',
});

const formatDuration = (seconds: number) => {
  const h = Math.floor(seconds / 3600);
  const m = Math.floor((seconds % 3600) / 60);
  return `${h}h ${m}m`;
};

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(amount);
};

const updateFilters = () => {
  router.get(route('reports.index'), {
    project_id: filterState.value.project_id === 'all' ? null : filterState.value.project_id,
    tag_id: filterState.value.tag_id === 'all' ? null : filterState.value.tag_id,
    start_date: filterState.value.start_date,
    end_date: filterState.value.end_date,
    include_billable: filterState.value.include_billable,
    include_non_billable: filterState.value.include_non_billable,
  }, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  });
};

watch(() => filterState.value, updateFilters, { deep: true });

const exportReport = (format: 'csv' | 'pdf') => {
  const params = new URLSearchParams();
  if (filterState.value.project_id !== 'all') params.append('project_id', filterState.value.project_id);
  if (filterState.value.tag_id !== 'all') params.append('tag_id', filterState.value.tag_id);
  params.append('start_date', filterState.value.start_date);
  params.append('end_date', filterState.value.end_date);
  params.append('include_billable', filterState.value.include_billable.toString());
  params.append('include_non_billable', filterState.value.include_non_billable.toString());
  params.append('format', format);

  const url = route('reports.export') + '?' + params.toString();
  if (format === 'csv') {
    window.location.href = url;
  } else {
    window.open(url, '_blank');
  }
};
</script>

<template>
  <Head title="Reports" />

  <MainLayout>
    <div class="min-h-screen bg-background p-6 lg:p-8">
      <div class="max-w-7xl mx-auto space-y-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div>
            <h1 class="text-3xl font-bold tracking-tight">Reports</h1>
            <p class="text-muted-foreground">Analyze your time tracking and earnings across projects and tags.</p>
          </div>
          <div class="flex items-center gap-2">
            <Button variant="outline" @click="exportReport('csv')">
              <Download class="mr-2 h-4 w-4" />
              CSV
            </Button>
            <Button variant="destructive" @click="exportReport('pdf')">
              <FileText class="mr-2 h-4 w-4" />
              PDF
            </Button>
          </div>
        </div>

        <Card>
          <CardHeader>
            <CardTitle>Filters</CardTitle>
            <CardDescription>Adjust the time range and categories to refine your report.</CardDescription>
          </CardHeader>
          <CardContent>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
              <div class="space-y-2">
                <label class="text-sm font-medium">Project</label>
                <Select v-model="filterState.project_id">
                  <SelectTrigger>
                    <SelectValue placeholder="All Projects" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="all">All Projects</SelectItem>
                    <SelectItem v-for="p in projects" :key="p.id" :value="p.id.toString()">
                      {{ p.name }}
                    </SelectItem>
                  </SelectContent>
                </Select>
              </div>

              <div class="space-y-2">
                <label class="text-sm font-medium">Tag</label>
                <Select v-model="filterState.tag_id">
                  <SelectTrigger>
                    <SelectValue placeholder="All Tags" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="all">All Tags</SelectItem>
                    <SelectItem v-for="t in tags" :key="t.id" :value="t.id.toString()">
                      #{{ t.name }}
                    </SelectItem>
                  </SelectContent>
                </Select>
              </div>

              <div class="space-y-2">
                <label class="text-sm font-medium">Start Date</label>
                <CustomDatePicker v-model="filterState.start_date" />
              </div>

              <div class="space-y-2">
                <label class="text-sm font-medium">End Date</label>
                <CustomDatePicker v-model="filterState.end_date" />
              </div>
            </div>

            <div class="flex items-center gap-6 mt-6">
              <div class="flex items-center space-x-2">
                <Checkbox id="billable" v-model:checked="filterState.include_billable" />
                <label for="billable" class="text-sm font-medium leading-none cursor-pointer">Billable</label>
              </div>
              <div class="flex items-center space-x-2">
                <Checkbox id="non-billable" v-model:checked="filterState.include_non_billable" />
                <label for="non-billable" class="text-sm font-medium leading-none cursor-pointer">Non-billable</label>
              </div>
            </div>
          </CardContent>
        </Card>

        <Tabs default-value="projects" class="w-full">
          <TabsList class="grid w-full max-w-md grid-cols-2">
            <TabsTrigger value="projects" class="flex items-center gap-2">
              <Folder class="h-4 w-4" />
              By Projects
            </TabsTrigger>
            <TabsTrigger value="tags" class="flex items-center gap-2">
              <TagIcon class="h-4 w-4" />
              By Tags
            </TabsTrigger>
          </TabsList>

          <TabsContent value="projects" class="mt-6">
            <Card>
              <CardHeader>
                <CardTitle class="flex items-center gap-2">
                  <BarChart3 class="h-5 w-5 text-primary" />
                  Project Breakdown
                </CardTitle>
              </CardHeader>
              <CardContent>
                <Table>
                  <TableHeader>
                    <TableRow>
                      <TableHead>Project Name</TableHead>
                      <TableHead class="text-right">Total Duration</TableHead>
                      <TableHead class="text-right">Estimated Earnings</TableHead>
                    </TableRow>
                  </TableHeader>
                  <TableBody>
                    <TableRow v-for="item in projectSummary" :key="item.id">
                      <TableCell class="font-medium">
                        <div class="flex items-center gap-2">
                          <div class="w-3 h-3 rounded-full" :style="{ backgroundColor: item.color || '#94a3b8' }"></div>
                          {{ item.name }}
                        </div>
                      </TableCell>
                      <TableCell class="text-right">{{ formatDuration(item.duration) }}</TableCell>
                      <TableCell class="text-right">{{ formatCurrency(item.earnings) }}</TableCell>
                    </TableRow>
                    <TableRow v-if="projectSummary.length === 0">
                      <TableCell colspan="3" class="h-24 text-center text-muted-foreground">
                        No data available for the selected filters.
                      </TableCell>
                    </TableRow>
                  </TableBody>
                </Table>
              </CardContent>
            </Card>
          </TabsContent>

          <TabsContent value="tags" class="mt-6">
            <Card>
              <CardHeader>
                <CardTitle class="flex items-center gap-2">
                  <TagIcon class="h-5 w-5 text-primary" />
                  Tag Breakdown
                </CardTitle>
              </CardHeader>
              <CardContent>
                <Table>
                  <TableHeader>
                    <TableRow>
                      <TableHead>Tag Name</TableHead>
                      <TableHead class="text-right">Total Duration</TableHead>
                      <TableHead class="text-right">Estimated Earnings</TableHead>
                    </TableRow>
                  </TableHeader>
                  <TableBody>
                    <TableRow v-for="item in tagSummary" :key="item.id">
                      <TableCell class="font-medium">#{{ item.name }}</TableCell>
                      <TableCell class="text-right">{{ formatDuration(item.duration) }}</TableCell>
                      <TableCell class="text-right">{{ formatCurrency(item.earnings) }}</TableCell>
                    </TableRow>
                    <TableRow v-if="tagSummary.length === 0">
                      <TableCell colspan="3" class="h-24 text-center text-muted-foreground">
                        No data available for the selected filters.
                      </TableCell>
                    </TableRow>
                  </TableBody>
                </Table>
              </CardContent>
            </Card>
          </TabsContent>
        </Tabs>
      </div>
    </div>
  </MainLayout>
</template>
