export function usePersonSchema(options: {
    name: string;
    url: string;
    jobTitle?: string;
    description?: string;
    image?: string;
    email?: string;
    sameAs?: string[];
}) {
    return {
        '@context': 'https://schema.org',
        '@type': 'Person',
        name: options.name,
        url: options.url,
        ...(options.jobTitle && { jobTitle: options.jobTitle }),
        ...(options.description && { description: options.description }),
        ...(options.image && { image: options.image }),
        ...(options.email && { email: `mailto:${options.email}` }),
        ...(options.sameAs?.length && { sameAs: options.sameAs }),
    };
}

export function useArticleSchema(options: {
    headline: string;
    url: string;
    datePublished: string;
    dateModified?: string;
    authorName: string;
    authorUrl?: string;
    description?: string;
    image?: string;
    publisherName?: string;
}) {
    return {
        '@context': 'https://schema.org',
        '@type': 'Article',
        headline: options.headline,
        url: options.url,
        datePublished: options.datePublished,
        ...(options.dateModified && { dateModified: options.dateModified }),
        ...(options.description && { description: options.description }),
        ...(options.image && { image: options.image }),
        author: {
            '@type': 'Person',
            name: options.authorName,
            ...(options.authorUrl && { url: options.authorUrl }),
        },
        ...(options.publisherName && {
            publisher: {
                '@type': 'Organization',
                name: options.publisherName,
            },
        }),
    };
}

export function useBreadcrumbSchema(items: Array<{ name: string; url: string }>) {
    return {
        '@context': 'https://schema.org',
        '@type': 'BreadcrumbList',
        itemListElement: items.map((item, index) => ({
            '@type': 'ListItem',
            position: index + 1,
            name: item.name,
            item: item.url,
        })),
    };
}

export function useWebSiteSchema(options: {
    name: string;
    url: string;
    description?: string;
}) {
    return {
        '@context': 'https://schema.org',
        '@type': 'WebSite',
        name: options.name,
        url: options.url,
        ...(options.description && { description: options.description }),
    };
}
