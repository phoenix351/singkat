<script setup>
import { cn } from "@/lib/utils";

const props = defineProps({
    RangeCalendarHeadingProps: Object,
    class: String,
});

const headingValue = ref(""); // Define your headingValue here

const delegatedProps = computed(() => {
    const { class: _, ...delegated } = props;

    return delegated;
});

const forwardedProps = useForwardProps(delegatedProps);
</script>

<template>
    <RangeCalendarHeading
        v-slot="{ headingValue }"
        :class="cn('text-sm font-medium', props.class)"
        v-bind="forwardedProps"
    >
        <slot :heading-value="headingValue">
            {{ headingValue }}
        </slot>
    </RangeCalendarHeading>
</template>
