<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

interface Props {
    correctAnswer?: string;
    allAnswers?: Array<string>;
}

const props = withDefaults(defineProps<Props>(), {
    correctAnswer: () => '0',
    allAnswers: () => [],
});

const form = useForm({
    radio: '',
});

const submit = () => {
    form.radio === props.correctAnswer || props.correctAnswer === '0' ? 
        form.get(route('playGame', {answer: form.radio, start: props.correctAnswer === '0'})) : 
        form.get(route('endGame'))
};
</script>

<template>
    <div>
        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <slot />
            <span v-for="(answer, index) in allAnswers" :key="index">
                <input type="radio" :id="'id' + answer" :name="'name' + answer" :value="answer" v-model="form.radio">
                <label :for="'id' + answer">{{ answer }}</label><br>
            </span>
            <Button type="submit" class="mt-4 w-full" :tabindex="4" :disabled="form.processing">
                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                <span v-if="correctAnswer!=='0' && allAnswers.length!==0">Answer question</span>
                <span v-else>Start new game</span>
            </Button>
        </form>
    </div>
</template>