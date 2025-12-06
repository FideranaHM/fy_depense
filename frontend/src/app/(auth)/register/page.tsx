//src/app/auth/register/page.tsx
import Link from "next/link";

export default function Register() {
    return (
        <main className="relative isolate flex min-h-screen items-center justify-center overflow-hidden bg-slate-50 dark:bg-slate-900 px-4">
            <button className='absolute top-3 left-4 z-[999] dark:text-gray-300 text-gray-800 '>
                <Link href='/'>
                    <span className="bg-gradient-to-r from-pink-500 to-blue-400 bg-clip-text text-transparent">
                        Acceuil
                    </span>
                </Link>
            </button>
            <div
                className="w-50 max-w-xl  rounded-xl flex flex-col md:flex-row items-center gap-8 transition-all duration-1000 
                'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'
                "
            >
                <div className="w-full flex-1 rounded-5xl bg-white/60 dark:bg-gray-800/60 shadow-2xl backdrop-blur-xl md:p-10">
                    <h1 className="text-center text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white md:text-4xl">
                        <>
                            Rejoindre{' '}
                            <span className="bg-gradient-to-r from-pink-600 to-blue-700 bg-clip-text text-transparent">
                                FyDepense
                            </span>
                        </>
                    </h1>
                    <p className="mt-2 text-center text-sm text-slate-600 dark:text-slate-300">
                        Créez votre compte en deux étapes simples
                    </p>
                    <form className="mt-8 space-y-5">
                        {/* nom */}
                        <div>
                            <label className="flex items-center text-sm font-medium text-slate-700 dark:text-slate-300">
                                Nom <span className="ml-1 text-red-400">*</span>
                            </label>
                            <input
                                type="text"
                                name="nom"
                                placeholder="Votre nom "
                                className="mt-1 block w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-slate-800 shadow-sm focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 dark:placeholder-slate-400"
                             />
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {/* prenom */}
                            <div className="space-y-2">
                                <label className="flex items-center text-sm font-medium text-slate-700 dark:text-slate-300">
                                    Prenom <span className="ml-1 text-red-400">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="prenom"
                                    placeholder="Votre prenom "
                                    className="mt-1 block w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-slate-800 shadow-sm focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 dark:placeholder-slate-400"
                                />
                            </div>
                            {/* Date de naissance*/}
                            <div className="space-y-2">
                                <label className="flex items-center text-sm font-medium text-slate-700 dark:text-slate-300 ">
                                    Date de naissance <span className="ml-1 text-red-400">*</span>
                                </label>
                                <input
                                    type="date"
                                    name="dateNaissance"
                                    placeholder="10-12-2025"
                                    className="block w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 pr-10 text-slate-800 shadow-sm focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 grid grid-cols-1 md:grid-cols-3 gap-6"
                                />
                            </div>
                        </div>

                        {/* email */}
                        <div>
                            <label className="flex items-center text-sm font-medium text-slate-700 dark:text-slate-300">
                                E-mail <span className="ml-1 text-red-400">*</span>
                            </label>
                            <input
                                type="text"
                                name="email"
                                placeholder="vous@exemple.com "
                                className="mt-1 block w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-slate-800 shadow-sm focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 dark:placeholder-slate-400"
                             />
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {/* password*/}
                            <div className="space-y-2">
                                <label className="flex items-center text-sm font-medium text-slate-700 dark:text-slate-300">
                                    Mot de passe <span className="ml-1 text-red-400">*</span>
                                </label>
                                <input
                                    type="password"
                                    name="password"
                                    placeholder="••••••••"
                                    className="block w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 pr-10 text-slate-800 shadow-sm focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200"
                                />
                            </div>
                            {/* confirm password*/}
                            <div className="space-y-2">
                                <label className="flex items-center text-sm font-medium text-slate-700 dark:text-slate-300">
                                    Confirmer le mot de passe <span className="ml-1 text-red-400">*</span>
                                </label>
                                <input
                                    type="password"
                                    name="password"
                                    placeholder="••••••••"
                                    className="block w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 pr-10 text-slate-800 shadow-sm focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200"
                                />
                            </div>
                        </div>
                    </form>  
                </div>   
            </div>       
        </main>
    );
}