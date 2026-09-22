import Alpine from 'alpinejs';
import {
    createIcons,
    Search,
    SearchX,
    House,
    Package,
    Plug,
    Boxes,
    Menu,
    Download,
    ArrowRight,
    ArrowLeft,
    ChevronDown,
    Check,
    SlidersHorizontal,
    X,
    Heart,
    FileText,
    Images,
    Info,
    Link,
    ExternalLink,
    Tags,
    Layers,
} from 'lucide';

createIcons({
    icons: {
        Search,
        SearchX,
        House,
        Package,
        Plug,
        Boxes,
        Menu,
        Download,
        ArrowRight,
        ArrowLeft,
        ChevronDown,
        Check,
        SlidersHorizontal,
        X,
        Heart,
        FileText,
        Images,
        Info,
        Link,
        ExternalLink,
        Tags,
        Layers,
    },
});

window.Alpine = Alpine;

Alpine.start();
