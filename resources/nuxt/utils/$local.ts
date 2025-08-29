

export const $local = {
  set(name: string,value: any) {
    return localStorage.setItem(name,JSON.stringify(value))
  },
  get(name: string) {
    if(localStorage.getItem(name) !== null) {
      try {
        return JSON.parse(localStorage.getItem(name)!);
      } catch {
        return localStorage.getItem(name);
      }
    }
    return null;
  },
  update(name: string,value: any) {
    if(this.get(name) !== null) {
      const data = this.get(name);
      if(Array.isArray(data)) {
        // data.push(value);
        value.forEach((e : any) => {
          data.push(e)
        });
      } else if(typeof(data) === "object") {
        // data[name] = value;
        Object.keys(value).forEach(e=> {
          // data[e] = value[e]
           $ofun(data).path({path :e,value :value[e]});
        })
      }

      if(data !== this.get(name)) {
        this.set(name,data);
      }
    } else if (typeof(value) === "object" && !Array.isArray(value)) {
        const data = {};
        Object.keys(value).forEach(e=> {
          // data[e] = value[e]
           $ofun(data).path({path :e,value :value[e]});
        })
        this.set(name,data);
      } else {
        this.set(name,value);
      }
  },
  delete(name: string) {
    localStorage.removeItem(name);
  }
}