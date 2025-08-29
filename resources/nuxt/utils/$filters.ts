export const $filters = {
  length({ item, max, min }: { item: string; max: number; min?: number }) {
    if (String(item).trim().length > max) {
      return false;
    }

    if (min !== undefined) {
      if (String(item).trim().length < min) {
        return false;
      }
    }

    return true;
  },
  empty(data: string | number | any[]) {
    switch (typeof data) {
      case "number":
      case "string": {
        if (!data) {
          return true;
        }
        break;
      }
      case "object": {
        if (!data.length) {
          return true;
        }
        break;
      }
    }
    return false;
  },
  range({ item, min, max }: { item: number; max: number; min: number }) {
    if (item > max) {
      return false;
    }
    if (item < min) {
      return false;
    }

    return true;
  },
};
