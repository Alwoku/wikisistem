import { createRouter, createWebHistory,isNavigationFailure } from 'vue-router';
import Index from '../components/index.vue';
import login from '../components/login.vue';
import NotFound from '../components/errors/404.vue';
import profile from '../components/pages/profile.vue';
import users from '../components/pages/users.vue';
import Forbidden from '../components/errors/403.vue'
import Groups from '../components/pages/groups.vue';
import Note from '../components/pages/note.vue';
import ExpansionFiles from '../components/pages/expansion-files.vue';
import DeletedObjects from '../components/pages/deleted-objects.vue';
import Folder from '../components/pages/folder.vue';
import GlobalFinder from '../components/pages/global-finder.vue';
import VersionsNotes from '../components/pages/versions-notes.vue';
import HistoryOfChanges from '../components/pages/history-of-changes.vue';
import SuggestedChanges from '../components/pages/suggested-changes.vue';
import Feedback from '../components/pages/feedback.vue';


const routes = [
    {
        path: '/',
        component: Index,
        meta:{
            title: 'Basis',
            breadcrumb: 'Home'
        }
    },
    { 
        path: '/:pathMatch(.*)*', 
        component: NotFound ,
        meta:{
            title: 'Ошибка'
        }
    },

    {
        path: '/403',
        name:"forbidden",
        component: Forbidden,
        meta:{
            title: 'Недостаточно прав доступа',
            breadcrumb: ''
        }
    },

    {
        path: '/profile/:id(\\d+)',
        name: 'profile',
        component: profile,
        meta:{
            title: 'Профиль пользователя',
            breadcrumb: ''
        }
    },


    {
        path: '/suggested-changes-to-objects',
        name: 'suggested-changes-to-objects',
        component: SuggestedChanges,
        meta:{
            title: 'Предлагаемые изменения',
            
        }
    },
    {
        path: '/settings',
        name: 'settings',
        beforeEnter: async (to, from, next) => {
            let isAdmin = await axios.get(route("settings.is-admin"));
            if(!isAdmin.data) { 
                return next({name : "forbidden"})
            } else{
                next()
            }
            
        },
        children: [

            {
                path: 'users',
                component: users,
                meta:{
                    title: 'Пользователи',
                     
                }
            },
            {
                path: 'groups',
                component: Groups,
                meta:{
                    title: 'Группы',
                    
                }
            },
            {
                path: 'expansion-files',
                component:ExpansionFiles,
                meta:{
                    title: 'Типы загружаемых файлов',
                     
                }
            },
            {
                path: 'deleted-objects',
                component: DeletedObjects,
                meta:{
                    title: 'Удаленные объекты',
                    
                }
            },
            {
                path: 'history-of-changes',
                name: 'history-of-changes',
                component: HistoryOfChanges,
                meta:{
                    title: 'История изменений сайта',
                    
                }
            },

            {
                path: 'suggested-changes',
                name: 'suggested-changes',
                component: SuggestedChanges,
                meta:{
                    title: 'Предлагаемые изменения',
                    
                }
            },

            {
                path: 'feedback',
                name: 'feedback',
                component: Feedback,
                meta:{
                    title: 'Обратная связь'
                }
            }

        ]
    },

    {
        path: '/objects',
        name: 'objects',
        beforeEnter: async (to, from, next) => {
            // todo: написать проверку доступа к объекту
            // let isAdmin = await axios.get(route("settings.is-admin"));
            // if(!isAdmin.data) { 
            //     return next({name : "forbidden"})
            // } else{
                next()
            // }
            
        },
        children: [

            {
                path: 'note/:id(\\d+)?',
                name: 'note',
                component: Note,
                meta:{
                    title: 'Статья',
                },

            },
            {
                name: 'folder',
                path: 'folder/:id(\\d+)?',
                component: Folder,
                meta:{
                    title: 'Папка',
                },
            
            },
            {
                name: 'global-finder',
                path: 'global/finder//:search',
                component: GlobalFinder,
                meta:{
                    title: 'Поиск',
                },
            
            },

            {
                name:'versions',
                path:'versions/:id(\\d+)',
                component: VersionsNotes,
                meta:{
                    title:'Версии'
                }
            }

        ]
    },

];

const router = createRouter({
    history: createWebHistory(),
    routes,
});
 
router.beforeEach((to,from,next) => {
    document.title = `${to.meta.title}`;
    next();
})
export default router;