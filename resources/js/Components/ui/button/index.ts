import { cva, type VariantProps } from 'class-variance-authority';

export { default as Button } from './Button.vue';

export const buttonVariants = cva(
  'inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-lg text-sm font-medium transition-all duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0',
  {
    variants: {
      variant: {
        default:
          'bg-accent text-white shadow-sm hover:bg-accent-hover active:scale-[0.98]',
        destructive:
          'bg-danger text-white shadow-sm hover:bg-danger/90 active:scale-[0.98]',
        outline:
          'border border-border bg-surface-raised shadow-sm hover:bg-surface-overlay hover:text-primary active:scale-[0.98]',
        secondary:
          'bg-surface-overlay text-primary shadow-sm hover:bg-border active:scale-[0.98]',
        ghost:
          'text-secondary hover:text-primary hover:bg-surface-overlay active:scale-[0.98]',
        link: 'text-accent-text underline-offset-4 hover:underline',
      },
      size: {
        default: 'h-9 px-4 py-2',
        sm: 'h-8 rounded-md px-3 text-xs',
        lg: 'h-10 rounded-md px-8',
        icon: 'h-9 w-9',
      },
    },
    defaultVariants: {
      variant: 'default',
      size: 'default',
    },
  }
);

export type ButtonVariants = VariantProps<typeof buttonVariants>;
