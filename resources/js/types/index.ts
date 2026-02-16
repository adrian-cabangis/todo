export * from './auth';
export * from './navigation';
export * from './ui';

import type { Auth, User } from './auth';

export type AppPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    name: string;
    auth: Auth;
    sidebarOpen: boolean;
    [key: string]: unknown;
};

export interface Task {
    id: number;
    user_id: number;
    title: string;
    description: string;
    deadline: string;
    status: string;
    priority: string;
    role: 'admin' | 'user';
    created_at: string;
    user?: User;
    attachments: Attachment[];
}

export interface Attachment {
    id: number;
    task_id: number;
    filename: string;
    filepath: string;
    mimetype: string;
    size: number;
    task?: Task;
}
