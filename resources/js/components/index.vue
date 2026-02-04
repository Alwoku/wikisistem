<!-- стартовая страница -->
<template>
    <div>
        <breadcrumbs-panel></breadcrumbs-panel>
        <h1 class="title is-2 has-text-centered">
            Добро пожаловать 
        </h1>
        
    </div>
</template>
<script>
import BreadcrumbsPanel from './menu/breadcrumbs-panel.vue';

export default {
    components:{BreadcrumbsPanel},
    created() {
        // запрашиваем данные
        axios.get(route("index")).then(response => {
            let data = response.data;

            if (data.idNote) {
                // если существует стартовая страница у группы, 
                // в которой состоит пользователь
                // перемещаем на эту страницу
                this.$router.push("/objects/note/"+data.idNote);
                return
            }
            
        })    
    },
}
</script>